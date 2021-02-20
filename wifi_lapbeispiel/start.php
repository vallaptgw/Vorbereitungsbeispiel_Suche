<?php
include 'config.php';
if (isset($_POST['safe']))
{
    try {
        $Schauspieler = $_POST['Schauspieler'];

        if (empty($Schauspieler))
        {
            echo 'Es wurde nichts gefunden, da nichts eingegeben wurde';
        }
        else
        {
            $suche = '%'.$Schauspieler.'%';

            $query = 'select f.titel as "Titel",
                             f.erscheinungsdatum as "Erscheinungs-Datum",
                             p.bezeichnung as "Produktionsfirma",
                             concat_ws(" ", s.vorname, s.nachname) as "Schauspieler"
                        from Film f, Produktionsfirma p, schauspieler s, Film_Schauspieler fs
                       where p.Produktionsfirma_id = f.Produktionsfirma_id
                         and f.film_id = fs.film_id
                         and fs.schauspieler_id = s.schauspieler_id
                         and concat_ws(" ", s.vorname, s.nachname) like ?
                    order by f.erscheinungsdatum';

            $stmt = $con->prepare($query);
            $stmt->execute([$suche]);
            $output = $stmt->fetch(PDO::FETCH_NUM);

            echo'Gesuchter Schauspieler: ' . $Schauspieler;
            echo'</br> </br>';
            echo'Gefundene Schauspieler: ' . $output[3];
            echo'</br> </br>';
            echo '<table class="table">
                    <thead><tr>';
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                echo '<th>' . $stmt->getColumnMeta($i)['name'] . '</th>';
            }
            echo '</tr></thead><tbody>';

            $stmt = $con->prepare($query);
            $stmt->execute([$suche]);
            $tableData = $stmt->fetchAll(PDO::FETCH_NUM);
            foreach($tableData as $row) {
                echo '<tr>';
                foreach($row as $column) {
                    echo "<td>$column</td>";
                }
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
    } catch (Exception $e)
    {
        echo $e->getCode().': '.$e->getMessage();
    }
}
else
{
    ?>
    <h1>Filmsuche</h1>
    <form method="post">
        <div class="form-group">
            <label class="col-sm-2" for="vn">Name des Schauspielers:</label>
            <input class="col-sm-5" type="text" id="nn" name="Schauspieler" placeholder="Geben Sie einen Text ein">
        </div>

        <input type="submit" name="safe" value="Suche">

    <?php
    echo '</form>';
}