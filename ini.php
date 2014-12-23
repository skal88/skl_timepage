<?php
/*
Plugin Name: skl TimePage Event
Plugin URI: http://trends.mediamarkt.es/wp-content/plugins/skl_timepage/readme/readme.html
Description: Plugin para añadir codigo Analytics y opción de añadir un evento a los X segundos para  evitar el rebote
Version: 1.2
Author: Albert Mulà Conesa
Author URI: http://albertmula.com
*/


add_action('admin_menu', 'skl_admin_timePage_menu');

function skl_admin_timePage_menu() {
	add_options_page('Analytics TimePage', 'Analytics', 'manage_options', __FILE__, 'skl_admin_timePage');
}

function skl_admin_timePage(){
	if(isset($_POST['action']) && $_POST['action'] == "save-skl-timepage"){  // Si existe el metodo POST
		$timepage = $_POST['timeGA-text'] * 1000;
        update_option('skl_timepage_time', $timepage); // Actualizamos los datos
        update_option('skl_timepage_code', $_POST['timeGA-code']); // Actualizamos los datos
        $time_switch = $_POST['timeGA-switch']?1:0;
        update_option('skl_timepage_switch', $time_switch); // Actualizamos los datos
        update_option('skl_timepage_event_name', $_POST['timeGA-name']); // Actualizamos los datos
        echo("<div class='updated message' style='padding: 10px; display:none'>Opciones guardadas.</div>"); // Imprimimos mensaje de guardado correctamente
    }
    // Obtenemos los datos
    $timepage_switch = get_option('skl_timepage_switch')==1?'checked':'';
    $timepage_code = stripslashes(get_option('skl_timepage_code'));
    $timepage_time_s = get_option('skl_timepage_time')/1000;
    $timepage_event_name = get_option('skl_timepage_event_name'); // TEST 
	?>

	<div class="wrap">
		<h2>Analytics</h2> <small>by: <a href="http://albertmula.com">Albert Mulà</a></small>
		<form method="post">
			<input type='hidden' name='action' value='save-skl-timepage'>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="timeGA-code">Codigo de Google Analytics</label></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Codigo de Google Analytics</span></legend>
							</fieldset>
							<textarea name="timeGA-code" rows="10" cols="50" id="timeGA-code" class="large-text code"><?=$timepage_code?></textarea>
						</td>
					</tr>
				</tbody>
			</table>

			<h3>Función evita rebote</h3>
			<p>Si activamos esta opción podremos configurar un evento para que pasados X segundos salte un evento, y así conseguir que google analytics no considere rebote a aquellos usuarios que han estado más de X segundos en nuestro site.</p>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="timeGA-switch">Activar evento</label></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Activar evento</span></legend>
							</fieldset>
							<input type="checkbox" name="timeGA-switch" id="timeGA-switch" <?=$timepage_switch?>>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="timeGA-event-box">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="timeGA-text">Tiempo para el evento</label></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span>Tiempo para el evento</span></legend>
								</fieldset>
								<input type="number" name="timeGA-text" id="timeGA-text" min="1" step="1" value="<?=$timepage_time_s?>" class="small-text"> Segundos
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="timeGA-name">Nombre para el evento</label></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span>Nombre para el evento</span></legend>
								</fieldset>
								<input type="text" name="timeGA-name" id="timeGA-name" min="1" step="1" value="<?=$timepage_event_name?>">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar cambios"></p>
		</form>
	</div>

	<? 
}



function skl_timepage_admin_footer(){
	// Añadimos script en el footer para la emergente de Guardado
	?>
<script type="text/javascript">
    var $ = jQuery.noConflict();
    $(function(){

    	// Show/hide Saved Dialog
        $(".updated").fadeIn();   
        setTimeout(function(){
            $(".updated").fadeOut();
        },2000);


        // 
        if(!$('#timeGA-switch').is(':checked')) { // Si no esta activado el checkbox
			$('#timeGA-text').prop('disabled', true); // Deshabilitamos la caja de texto del tiempo
			$('#timeGA-name').prop('disabled', true); // Deshabilitamos la caja de texto del nombre
			$('#timeGA-event-box').hide(); // Ocultamos el Div que engloba las cajas de texto del tiempo y el nombre
		}

	    $('#timeGA-switch').click(function(){ // Al hacer click sobre el checkbox...
	     	if($('#timeGA-switch').is(':checked')) { // Si esta checkeado
	     		$('#timeGA-event-box').slideDown(); // Mostramos el div que engloba las cajas de texto (Animacion hacia abajo)
	            $('#timeGA-text').prop('disabled', false); // Habilitamos la caja de texto del tiempo
	            $('#timeGA-name').prop('disabled', false); // Habilitamos la caja de texto del nombre
	        } else {  // Si no está checkeado
	            $('#timeGA-text').prop('disabled', true); // Deshabilitamos la caja de texto del tiempo
	            $('#timeGA-name').prop('disabled', true); // Deshabilitamos la caja de texto del nombre
	            $('#timeGA-event-box').slideUp(); // Ocultamos el div que engloba las cajas de texto (Animación hacia arriba)
	        }
	    });
    });
</script>
	<?
} // skl_timepage_admin_footer
add_action('admin_footer', 'skl_timepage_admin_footer');


function skl_timepage_footer(){
	// Pintamos codigo al final de la página
	?>
<script>
	<?
	echo stripslashes(get_option('skl_timepage_code'));
	
	if (is_single() || is_page()) {
		//Si es un post o una página
		$pagetime_switch = get_option('skl_timepage_switch'); // Obtenemos el valor del switch
		if( $pagetime_switch == 1 ){ // si es uno está activado
	?>
  setTimeout("ga('send','event','<?=get_option('skl_timepage_event_name')?>','+<?=(get_option('skl_timepage_time')/1000)?> secs')",<?=get_option('skl_timepage_time')?>);
	<?
		} // $pageswitch == 1
	} // END - is_single() || is_page()

	?>
</script>
	<?
} // skl_timepage_footer
add_Action('wp_footer', 'skl_timepage_footer');

?>