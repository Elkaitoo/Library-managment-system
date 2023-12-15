

<?php
include 'db.php';
$cprque="SELECT * FROM `users`  WHERE cpr='{$_GET['q']}'";

$res = $db->query($cprque);
if ($row=$res->fetch()) {
    echo "The CPR is already taken";
}
else
echo "Valid";





?>