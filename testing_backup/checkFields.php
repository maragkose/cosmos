<?php
include_once "Form.php";
include_once "utils/LoadForm.php";

if(!isset($_GET["test"]))
    die("Indica el nombre del test a validar: checkFields.php?test=nombre_test");

try{

$loader = new loadForm($_GET["test"]);
$form = $loader->loadForm();
} catch (Exception $e) {
    switch ($e->getCode()) {
        case FORM_NOT_FOUND:
            die($e->getMessage());
            break;
        case NO_READABLE_FORM_DIR:
            break;
    }
}
// Recupera y codifica todos los identificadores de ítems del test
$formFields = generateItemCodes::generateAll($form->getItemsGroups());
$fieldIdErrors=array();

foreach($formFields as $field){
    $isAlnum = ctype_alnum(str_replace("_", "", $field));
    if(!$isAlnum)
        $fieldIdErrors[]=$field;
}
?>
<div class="form-title">
            <h2>Testmaker: validación de archivos de test</h2>
        </div>
<?php if($fieldIdErrors){?>
<p>Los siguientes identificadores contienen caracteres no permitidos:<p>
<ul>
    <?php foreach($fieldIdErrors as $field){?>
    <li><?php echo $field;?></li>
    <?php }?>
</ul>
<?php }else{?>
<p>No se encontraron problemas en la validación de identificadores.</p>
<?php } ?>
