<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

include '../../functions.php';
include '../../config.php';

//New PDO DB connection
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

@session_start();

$gibbonPersonFieldID = $_GET['gibbonPersonFieldID'];
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address'])."/userFields_edit.php&gibbonPersonFieldID=$gibbonPersonFieldID";

if (isActionAccessible($guid, $connection2, '/modules/User Admin/userFields_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if school year specified
    if ($gibbonPersonFieldID == '') {
        $URL .= '&return=error1';
        header("Location: {$URL}");
    } else {
        //Validate Inputs
        $name = $_POST['name'];
        $active = $_POST['active'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $options = '';
        if (isset($_POST['options'])) {
            $options = $_POST['options'];
        }
        $required = $_POST['required'];
        $activePersonStudent = 0;
        $activePersonStaff = 0;
        $activePersonParent = 0;
        $activePersonOther = 0;
        if (isset($_POST['roleCategories']) && is_array($_POST['roleCategories'])) {
            foreach ($_POST['roleCategories'] as $roleCategory) {
                if ($roleCategory == 'activePersonStudent') {
                    $activePersonStudent = 1;
                }
                if ($roleCategory == 'activePersonStaff') {
                    $activePersonStaff = 1;
                }
                if ($roleCategory == 'activePersonParent') {
                    $activePersonParent = 1;
                }
                if ($roleCategory == 'activePersonOther') {
                    $activePersonOther = 1;
                }
            }
        }
        $activeDataUpdater = $_POST['activeDataUpdater'];
        $activeApplicationForm = $_POST['activeApplicationForm'];

        if ($name == '' or $active == '' or $description == '' or $type == '' or $required == '' or $activeDataUpdater == '' or $activeApplicationForm == '') {
            $URL .= '&return=error3';
            header("Location: {$URL}");
        } else {
            //Write to database
            try {
                $data = array('name' => $name, 'active' => $active, 'description' => $description, 'type' => $type, 'options' => $options, 'required' => $required, 'activePersonStudent' => $activePersonStudent, 'activePersonStaff' => $activePersonStaff, 'activePersonParent' => $activePersonParent, 'activePersonOther' => $activePersonOther, 'activeDataUpdater' => $activeDataUpdater, 'activeApplicationForm' => $activeApplicationForm, 'gibbonPersonFieldID' => $gibbonPersonFieldID);
                $sql = 'UPDATE gibbonPersonField SET name=:name, active=:active, description=:description, type=:type, options=:options, required=:required, activePersonStudent=:activePersonStudent, activePersonStaff=:activePersonStaff, activePersonParent=:activePersonParent, activePersonOther=:activePersonOther, activeDataUpdater=:activeDataUpdater, activeApplicationForm=:activeApplicationForm WHERE gibbonPersonFieldID=:gibbonPersonFieldID';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                $URL .= '&return=error2';
                header("Location: {$URL}");
                exit();
            }

            $URL .= '&return=success0';
            header("Location: {$URL}");
        }
    }
}
