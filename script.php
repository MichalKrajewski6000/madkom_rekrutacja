<?php
ini_set('max_execution_time', 0);

$header = [
    "User-Agent: Madkom REST Web-App"
];

$org = $_GET['org'];
$sort = $_GET['sort_type'];

function name_compare_desc($r1, $r2) {
    return $r1["name"] < $r2["name"] ? 1 : -1;
}

function name_compare_asc($r1, $r2) {
    return $r1["name"] > $r2["name"] ? 1 : -1;
}

function contributor_compare_desc($r1, $r2) {
    return $r1["contributors"] < $r2["contributors"] ? 1 : -1;
}

function contributor_compare_asc($r1, $r2) {
    return $r1["contributors"] > $r2["contributors"] ? 1 : -1;
}

function count_collabs($repo, $header) {

    $ch = curl_init("https://api.github.com/repos/".$repo."/contributors");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($response);

    if(gettype($data) === 'array') {
        return count($data);
    }
    else {
        return 0;
    }
    
    
}

function getParent($repo, $header) {
    $ch = curl_init("https://api.github.com/repos/".$repo);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($response);

    $parent = $data->parent;

    return $parent;
}

$curl = curl_init("https://api.github.com/orgs/".$org."/repos");

curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);

curl_close($curl);
//var_dump($response);
echo '<h3>'.$org.'</h3>';
$data = json_decode($response);

$i=1;

$repos = [];

foreach ($data as $repo) {
    $full_name = $repo->full_name;
    $name = $repo->name;
    $url = $repo->html_url;
    $fork = $repo->fork;
    if($fork === false || $fork === NULL) {
        $is_fork = 'Nie';
        $parentName = '-';
        $parentUrl = null;
    }
    else {
        $is_fork = 'Tak';
        $parent = getParent($full_name, $header);
        $parentName = $parent->name;
        $parentUrl = $parent->html_url;
    }
    $contributors = count_collabs($full_name, $header);
    $record = array(
        "name" => $name,
        "contributors" => $contributors,
        "url" => $url,
        "isFork" => $is_fork,
        "parentName" => $parentName,
        "parentUrl" => $parentUrl
        
    );
    array_push($repos,$record);
    $i++;
}

switch($sort) {
    case 'name_asc':
        usort($repos, "name_compare_asc");
        break;
    case 'name_desc':
        usort($repos, "name_compare_desc");
        break;
    case 'cont_asc':
        usort($repos, "contributor_compare_asc");
        break;
    case 'cont_desc':
        usort($repos, "contributor_compare_desc");
        break;
}

echo '<table border=1>
<tr><td>Repozytorium</td><td>Kontrybutorzy</td><td>Fork</td><td>Parent Repository</td></tr>';
foreach ($repos as $repo) {
    
    echo '<tr><td><a href="'.$repo["url"].'">'.$repo["name"].'</a></td><td>'.$repo["contributors"].'</td><td>'.$repo["isFork"].'</td><td><a href="'.$repo["parentUrl"].'">'.$repo["parentName"].'</a></td></tr>';
    
}
echo '</table>';


echo '
<form action="index.php">
    <input type="submit" value="Wróć na stronę główną" />
</form>';
?>