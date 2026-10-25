<?php
// if (isset($_POST['surname']))
// {
//     require 'validate.inc';

//     $errors = array();

//     validateName($errors, $_POST, 'surname');

//     if ($errors)
//     {
//         echo '<h1>Invalid, correct the following errors:</h1>';

//         foreach ($errors as $field => $error)
//             echo "$field $error</br>";

//         // tampilkan kembali form
//         include 'form.inc';
//     }
//     else
//     {
//         echo 'Form submitted successfully with no errors';
//     }
// }
// else
//     // tampilkan kembali form
//     include 'form.inc';


if (isset($_POST['surname']))
{
    require 'validate.inc';

    $errors = array();

    validateName($errors, $_POST, 'surname');
    validateEmail($errors, $_POST, 'email');
    

    if ($errors)
    {
        echo '<h1>Invalid, correct the following errors:</h1>';

        foreach ($errors as $field => $error)
            echo "$field $error<br/>";

        include 'form.inc';
    }
    else
    {
        echo 'Form submitted successfully with no errors';
    }
}
else
{
    include 'form.inc';
}