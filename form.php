<?php

// if the from is loaded from WordPress form loader plugin,
// the phpfmg_display_form() will be called by the loader
if( !defined('FormmailMakerFormLoader') ){
    # This block must be placed at the very top of page.
    # --------------------------------------------------
	require_once( dirname(__FILE__).'/form.lib.php' );
    phpfmg_display_form();
    # --------------------------------------------------
};


function phpfmg_form( $sErr = false ){
		$style=" class='form_text' ";

?>


<script type="text/javascript">
    
		function countChar(val) {
			var len = val.value.length;
			//console.log(len);
			if (len >= 300) {
			   alert('Você excedeu o número máximo de catacteres.')
			   val.value = val.value.substring(0, 299);
			} else {
			  currLen = 300 - len;
			  $('#charNum').text('Limite de caracteres: ' + currLen);
			}
      }
    
</script>

<div id='frmFormMailContainer'>

<form name="frmFormMail" id="frmFormMail" target="submitToFrame" action='<?php echo PHPFMG_ADMIN_URL . '' ; ?>' method='post' enctype='multipart/form-data' onsubmit='return fmgHandler.onSubmit(this);'>

<input type='hidden' name='formmail_submit' value='Y'>
<input type='hidden' name='mod' value='ajax'>
<input type='hidden' name='func' value='submit'>

            
<ol class='phpfmg_form' >

<li class='field_block' id='field_0_div'><div class='col_label'>
	<label class='form_field'>Nome</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_0"  id="field_0" value="<?php  phpfmg_hsc("field_0", ""); ?>" class='text_box'>
	<div id='field_0_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_1_div'><div class='col_label'>
	<label class='form_field'>Email</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_1"  id="field_1" value="<?php  phpfmg_hsc("field_1", ""); ?>" class='text_box'>
	<div id='field_1_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_2_div'><div class='col_label'>
	<label class='form_field'>Telefone</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_2"  id="field_2" value="<?php  phpfmg_hsc("field_2", ""); ?>" class='text_box'>
	<div id='field_2_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_3_div'><div class='col_label'>
	<label class='form_field'>Celular</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_3"  id="field_3" value="<?php  phpfmg_hsc("field_3", ""); ?>" class='text_box'>
	<div id='field_3_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_4_div'><div class='col_label'>
	<label class='form_field'>Cidade</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_4"  id="field_4" value="<?php  phpfmg_hsc("field_4", ""); ?>" class='text_box'>
	<div id='field_4_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_5_div'><div class='col_label'>
	<label class='form_field'>Assunto</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_5"  id="field_5" value="<?php  phpfmg_hsc("field_5", ""); ?>" class='text_box'>
	<div id='field_5_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_6_div'><div class='col_label'>
	<label class='form_field'>Mensagem</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<textarea name="field_6" id="field_6" rows=4 cols=25 class='text_area' style="height:120px" OnKeyUp="return verificachars()"><?php  phpfmg_hsc("field_6"); ?></textarea>

	<div id='field_6_tip' class='instruction'></div>
	<div id="charNum">Limite de caracteres: 300</div>
	</div>
</li>


<li class='field_block' id='phpfmg_captcha_div'>
	<div class='col_label'></div><div class='col_field'>
	<?php phpfmg_show_captcha(); ?>
	</div>
</li>


            <li>
            <div class='col_label'>&nbsp;</div>
            <div class='form_submit_block col_field'>
	

                <input type='submit' value='Enviar' class='form_button'>

				<div id='err_required' class="form_error" style='display:none;' align="center">
				    <label class='form_error_title'>Por favor, cheque os campos obrigatórios</label>
				</div>
				


                <span id='phpfmg_processing' style='display:none;'>
                    <img id='phpfmg_processing_gif' src='<?php echo PHPFMG_ADMIN_URL . '?mod=image&amp;func=processing' ;?>' border=0 alt='Processing...'> <label id='phpfmg_processing_dots'></label>
                </span>
            </div>
            </li>

</ol>
</form>

<iframe name="submitToFrame" id="submitToFrame" src="javascript:false" style="position:absolute;top:-10000px;left:-10000px;" /></iframe>

</div>
<!-- end of form container -->

<!-- [Your confirmation message goes here] -->
<div id='thank_you_msg' style='display:none;color:#3C763D;border-style:dashed;border-width:1;pxborder-color:#3C763D;font-size:14px;padding-top:10px;padding-bottom:10px' align='center'>
Seu contato foi enviado. Obrigado!
</div>


            






<?php

    phpfmg_javascript($sErr);

}
# end of form




function phpfmg_form_css(){
    $formOnly = isset($GLOBALS['formOnly']) && true === $GLOBALS['formOnly'];
?>
<style type='text/css'>
<?php 
if( !$formOnly ){
    echo"
body{
    margin-left: 18px;
    margin-top: 18px;
}

body{
    font-family : Verdana, Arial, Helvetica, sans-serif;
    font-size : 13px;
    color : #474747;
    background-color: transparent;
}

select, option{
    font-size:13px;
}
";
}; // if
?>

ol.phpfmg_form{
    list-style-type:none;
    padding:0px;
    margin:0px;
}

ol.phpfmg_form input, ol.phpfmg_form textarea, ol.phpfmg_form select{
	display: block;
    width: 100%;
    height: 44px;
    border-radius: 10px;
    font-size: 16px;
    outline: 0;
    box-shadow: none;
    border: 2px solid #F19F1F;
    margin-bottom: 15px;
	padding-left: 10px;
	padding-right: 10px;
	padding-top: 10px;
	padding-bottom: 10px;
}

ol.phpfmg_form li{
    margin-bottom:5px;
    clear:both;
    display:block;
    overflow:hidden;
	width: 100%
}


.form_field, .form_required{
    font-weight : bold;

}

.form_required{
    color:red;
    margin-right:8px;
}

.field_block_over{
}

.form_submit_block{
    padding-top: 3px;
}

.text_box,.text_select {
    height: 32px;
}

.text_box, .text_area, .text_select {
    min-width:160px;
    max-width:500px;
    width: 100%;
    margin-bottom: 10px;
}

.text_area{
    height:80px;
}

.form_error_title{
    font-weight: bold;
    color: red;
}

.form_error{
    background-color: #F4F6E5;
    border: 1px dashed #ff0000;
    padding: 10px;
    margin-bottom: 10px;
}

.form_error_highlight{
    
    border-bottom: 1px dashed #ff0000;
}

div.instruction_error{
    color: red;
    font-weight:bold;
}

hr.sectionbreak{
    height:1px;
    color: #ccc;
}

#one_entry_msg{
    background-color: #F4F6E5;
    border: 1px dashed #ff0000;
    padding: 10px;
    margin-bottom: 10px;
}


#frmFormMailContainer input[type="submit"]{
    padding: 10px 25px; 
    font-weight: bold;
    margin-bottom: 10px;
    background-color: #FAFBFC;
}

#frmFormMailContainer input[type="submit"]:hover{
    background-color: #E4F0F8;
}

<?php phpfmg_text_align();?>    



</style>

<?php
}
# end of css
 
# By: formmail-maker.com
?>