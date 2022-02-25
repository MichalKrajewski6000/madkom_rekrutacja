<?php

echo '
<form action="script.php" method="GET">
    Wpisz nazwę organizacji:<br>
    <input type="text" name="org" required /><br>
    Sortowanie listy:<br>
    <select name="sort_type">
    <option value="name_asc">Nazwa repozytorium rosnąco</option>
    <option value="name_desc">Nazwa repozytorium malejąco</option>
    <option value="cont_asc">Liczba kontrybutorów rosnąco</option>
    <option value="cont_desc">Liczba kontrybutorów malejąco</option>
    </select>
       <br>
    <input type="submit" value="Pokaż repozytoria" />
</form>';

?>