<?php

    foreach($alertas as $key => $alerta):
        foreach($alerta as $mensaje):
?>
    <!--Agrega una div con una clase error y la alerta-->
    <div class="alerta  <?php echo $key;?>"><?php echo $mensaje?></div> 
<?php
        endforeach;
    endforeach;
?>