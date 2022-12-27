<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "links@yourhost.com.br" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "ccd2bc" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;display:none">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'B0C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHVqRxQKmMIYwOgRMRRFrZW1lbRAIRVUn0ujawADTC3ZSaNS0lamrVi1Fdh+aOqh52MTAdmBzC4oY1M2hAYMg/KgIsbgPAFg9zSk1h93OAAAAAElFTkSuQmCC',
			'A998' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWl0bQh0EEESC2gFiQXA1IGdFLV06dLMzKipWUjuC2hlDHQICUAxLzSUodEBwzyWRkcMMUy3AM3DcPNAhR8VIRb3AQD5js0WjK4chQAAAABJRU5ErkJggg==',
			'179E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUMDkMRYHRgaHR0dHZDViQLFXBsCHVD1MrSyIsTATlqZtWrayszI0Cwk9wHVBTCEoOsFimKYx9rAiCEm0sCI7pYQkQYGNDcPVPhREWJxHwC9xMbwVlha0wAAAABJRU5ErkJggg==',
			'5C28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGaY6IIkFNLA2Ojo6BASgiIk0uDYEOoggiQUGgHgBMHVgJ4VNm7Zq1cqsqVnI7msFqmtlQDEPLDaFEcW8AKCYQwCqmMgUoFscUPWyBjCGsoYGoLh5oMKPihCL+wDsTsyOzH7x7wAAAABJRU5ErkJggg==',
			'9DF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDAxoCkMREpoi0sjYwNCKLBbSKNLo2MLRiEZsSgOS+aVOnrUwNXRUVheQ+VleQOkYHZL0MYL2MoSFIYgIQ87C5BUUM7GY0sYEKPypCLO4DAGLqzdnREoEjAAAAAElFTkSuQmCC',
			'0165' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QsQ2AMAwE7cIbeCCnoH+KNJkmDRskI1DAlAQqR6EEKX5Xp7d0Mp3DZJopv/ixEShyhGMCBodgvqdFILln2KgxXsz5pb1tPVJyfk8vWNbhFh3TcrPVtHOh5mLwfmwSKVK1Cf73YV78Llc6yIiHEFrFAAAAAElFTkSuQmCC',
			'7ABA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGVpRRFsZQ1gbHaY6oIixtrI2BAQEIItNEWl0bXR0EEF2X9S0lamhK7OmIbmP0QFFHRiyNoiGujYEhoYgiYk0ANU1BKKoC2jA1AsWC2VEERuo8KMixOI+AKgozM0Nz07AAAAAAElFTkSuQmCC',
			'1D4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHUMdkMRYHURaGVodHQKQxEQdRBodpjo6iKDoBYoFwsXATlqZNW1lZmZm1jQk94HUuTZi6nUNDcQ0D1NdK9B9qG4JwXTzQIUfFSEW9wEAjITKJKKNIBgAAAAASUVORK5CYII=',
			'D13C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhDGaYGIIkFTGEMYG10CBBBFmtlDWBoCHRgQRFjCGBodHRAdl/U0lVRq6auzEJ2H5o6hBjQPGxiKHZMYcBwS2gAayi6mwcq/KgIsbgPAD8Wy25atOYMAAAAAElFTkSuQmCC',
			'0D88' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxBoi0Mjo6BAQgiYlMEWl0bQh0EEESC2gVaXREqAM7KWrptJVZoaumZiG5D00dXAzdPGx2YHMLNjcPVPhREWJxHwB1qcxz/9nxogAAAABJRU5ErkJggg==',
			'D04A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHVqRxQKmMIYwtDpMdUAWa2VtZZjqEBCAIibS6BDo6CCC5L6opdNWZmZmZk1Dch9InWsjXB1CLDQwNATdDnR1ILegiUHcjCo2UOFHRYjFfQBjCM3IjNV7YAAAAABJRU5ErkJggg==',
			'DCCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCHUMdkMQCprA2OjoEOgQgi7WKNLg2CDqIoImxNjDC1IGdFLV02qqlq1aGZiG5D00dihi6eRh2YHELNjcPVPhREWJxHwDud82M/TgRfQAAAABJRU5ErkJggg==',
			'9B04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQximMDQEIImJTBFpZQhlaEQWC2gVaXR0dGhFE2tlbQiYEoDkvmlTp4YtXRUVFYXkPlZXkLpAB2S9DEDzXBsCQ0OQxAQgdmBzC4oYNjcPVPhREWJxHwBGwc3VDN2sCAAAAABJRU5ErkJggg==',
			'8575' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MDkMREpogAyUAHZHUBrZhiQHUhDI2Org5I7lsaNXXpqqUro6KQ3CcyhaHRYQqQRjEPKBaALibS6OjA6CCCYgdrK2sDQwCy+1gDGEOAYlMdBkH4URFicR8A/UvMA9eOrc8AAAAASUVORK5CYII=',
			'E7B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNEQ11DGVqRxQIaGBpdGx2mOqCLNQQEBKCKtbI2OjqIILkvNGrVtKWhK7OmIbkPqC4ASR1UjNGBtSEQTYwVCNHtEGlgRXNLaAhQDM3NAxV+VIRY3AcApqDODDa600wAAAAASUVORK5CYII=',
			'313B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGUMdkMQCpjAGsDY6OgQgq2xlDWBoCHQQQRabwhDAgFAHdtLKqFVRq6auDM1Cdh+qOqh5DJjmYRELAOpFd4toAGsoupsHKvyoCLG4DwAbHsm7BzQ6uAAAAABJRU5ErkJggg==',
			'A595' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2Quw2AMAwF7SIbmH2cgt5IcUE2YAtTZAMYgQKmJKUjKEGKX3fy52S4HmXQU37xQx4UFFUcC0KGMbLvo40s2NQwKZQqG9n55WM/zmXO2flJgZWTGLlZ1cqsZXXfGuuNloWCkUUahgkUdu7gfx/mxe8GfOvMIayheRoAAAAASUVORK5CYII=',
			'ED21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGVqRxQIaRFoZHR2mook1ujYEhKKLOTQEwPSCnRQaNW1l1sqspcjuA6trxbCj0WEKFrEALG5xQBUDuZk1NCA0YBCEHxUhFvcBABTQzbGcz7qLAAAAAElFTkSuQmCC',
			'9F67' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUNDkMREpog0MDo6NIggiQW0ijSwNmATA9JI7ps2dWrY0qmrVmYhuY/VFajO0aEVxWaw3oApyGICELEABgy3ODqguhnoilBGFLGBCj8qQizuAwCO0MtfW8Xq+AAAAABJRU5ErkJggg==',
			'0BB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUMDkMRYA0RaWRsdHZDViUwRaXRtCEQRC2gFq3N1QHJf1NKpYUtDV0ZFIbkPos6hQQRVL9C8ABQxmB0iGG5xCEB2H8TNDFMdBkH4URFicR8A3M3MJwdajBgAAAAASUVORK5CYII=',
			'254D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxkaHUMdkMREpog0MLQ6OgQgiQW0AsWmOjqIIOtuFQlhCISLQdw0berSlZmZWdOQ3RfA0OjaiKqX0QEoFhqIIsbaINLogKZOpIG1Feg+FLeEhjKGoLt5oMKPihCL+wCDI8uxWFiNNQAAAABJRU5ErkJggg==',
			'80C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHaY6IImJTGEMYXQICAhAEgtoZW1lbRB0EEFRJ9LoCqKR3Lc0atrKVCAdheQ+qLpGBxTzwGKtDBh2CExhwOIWTDc7hoYMgvCjIsTiPgAuMswj/eQQBQAAAABJRU5ErkJggg==',
			'451D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMkxhDHVAFgsRAWJGhwAkMUagGCNQTARJjHWKSAhQL0wM7KRp06YuXTVtZdY0JPcFTGFodJiCqjc0FFOMYYoIFjHWVpAdAShiQJeEOqK6eaDCj3oQi/sA6AzKmMF/OGcAAAAASUVORK5CYII=',
			'3F5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RANEQ11DHUMdkMQCpog0sDYwOgQgq2yFiIkgi4HUTYWrAztpZdTUsKWZmaFZyO6bAtIViGEeSEwEww5UMZBbGB0dUfSKBgBVhDKiuHmgwo+KEIv7ALXtyvsXeCiwAAAAAElFTkSuQmCC',
			'2422' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAe0lEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaY6IImJTGGYyujoEBCAJBYAVMXaEOgggqy7ldEVKNMgguy+aUuXrlqZtSoK2X0BIq1AWxqR7WB0EA11mAIURXYL0ESGAIYpyGIiIFscgKJIYqGhDK2soYGhIYMg/KgIsbgPAI43ysrtAP2MAAAAAElFTkSuQmCC',
			'0E04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMDQEIImxBog0MIQyNCKLiUwRaWB0dGhFFgtoFWlgbQiYEoDkvqilU8OWroqKikJyH0RdoAOm3sDQEEw7sLkFRQybmwcq/KgIsbgPAN6YzMdP5zQ/AAAAAElFTkSuQmCC',
			'F3CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNZQxhCHUMDkMQCGkRaGR0CHRhQxBgaXRsE0cVaWRsYYWJgJ4VGrQpbumplaBaS+9DUIZmHTQzdDmxuwXTzQIUfFSEW9wEA0tTLJDuC4cQAAAAASUVORK5CYII=',
			'0958' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHaY6IImxBrC2sjYwBAQgiYlMEWl0BaoWQRILaAWKTYWrAzspaunSpamZWVOzkNwX0MoY6NAQgGJeQCtDo0NDIIp5IlNYgHagioHcwujogKIX5GaGUAYUNw9U+FERYnEfAO0My/kivNxzAAAAAElFTkSuQmCC',
			'7E05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMIYGIIu2ijQwhDI6MKCJMTo6oopNEWlgbQh0dUB2X9TUsKWrIqOikNzH6ABSF9AggqSXtQFTTKQBYgeyGFhFKENAAIoYyM0MUx0GQfhREWJxHwDyZsqt8k7qdgAAAABJRU5ErkJggg==',
			'31B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGaY6IIkFTGEMYG10CAhAVtnKGsDaEOgggiw2hQGkrkEEyX0ro1ZFLQ1dtSoK2X0QdY0OKOYBxRoCWhkwxaYwoLgFrDcA1c2soayhjKEhgyD8qAixuA8ASK7KoOtyzYkAAAAASUVORK5CYII=',
			'2178' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6Y6IImJTGEMYGgICAhAEgtoBapsCHQQQdbdyhDA0OgAUwdx07RVUauWrpqahew+oB0MUxhQzGN0AIkyopjH2gASQRUDsgNA4sh6Q0NZQ4FiKG4eqPCjIsTiPgAWOMl1jQhuYQAAAABJRU5ErkJggg==',
			'915B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjAGsDYwOgQgiQW0soLFRFDEgHqnwtWBnTRt6qqopZmZoVlI7mN1ZQhgaAhEMY+hFSKGbJ4AyDw0MZEpDAGMjo4oeoEuCWUIZURx80CFHxUhFvcBAMCNyJEovksOAAAAAElFTkSuQmCC',
			'DD5C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHaYGIIkFTBFpZW1gCBBBFmsVaXRtYHRgQRebyuiA7L6opdNWpmZmZiG7D6TOoSHQgQFNLzYxV6AYih1AtzA6OqC4BeRmhlAGFDcPVPhREWJxHwAFfs2lU8LetAAAAABJRU5ErkJggg==',
			'86F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZW1gCAhAEgtoFWlkbWB0EEFRJ9KAJAZ20tKoaWFLQ1dFhSG5T2SKKMi8qSJo5rmC5DDF0OzAdAvYzUDzkN08UOFHRYjFfQAqqMt111Kf9gAAAABJRU5ErkJggg==',
			'5E5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHUMdkMQCGkQaWBsYHQKwiIkgiQUGAMWmwtWBnRQ2bWrY0szM0Cxk97WCdAWimAcTQzYvoBVkB6qYyBSRBkZHRxS9rAGioQyhjChuHqjwoyLE4j4A04DK9xlBQNsAAAAASUVORK5CYII=',
			'0840' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHVqRxVgDWFsZWh2mOiCJiUwRaQSKBAQgiQW0AtUFOjqIILkvaunKsJWZmVnTkNwHUsfaCFcHFRNpdA0NRBED29GIagfYLY2obsHm5oEKPypCLO4DAKrQzKLvDGP4AAAAAElFTkSuQmCC',
			'374D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RANEQx0aHUMdkMQCpjA0OrQ6OgQgq2wFik11dBBBFpsCFA2Ei4GdtDJq1bSVmZlZ05DdN4UhgLURTW8rowNraCCaGGsDA5q6gCkiYDFkt4gGgMVQ3DxQ4UdFiMV9AOpVy9fWFiVJAAAAAElFTkSuQmCC',
			'E647' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHUNDkMQCGlhbGVodGkRQxEQaGaZiiDUwBDoAaYT7QqOmha3MzFqZheS+gAbRVtZGh1YGNPNcQwOmoIs5NDoEMKC7pdHRAYubUcQGKvyoCLG4DwBpvs3OyJY5HQAAAABJRU5ErkJggg==',
			'5FB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQ11DGaY6IIkFNIg0sDY6BASgizUEOoggiQUGgNQ5wsTATgqbNjVsaeiqqDBk97WCzZuKrBcsBjYVyQ6IGIodIlMw3cIKshfNzQMVflSEWNwHAETMzPaRC8jYAAAAAElFTkSuQmCC',
			'0EDA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGVqRxVgDRBpYGx2mOiCJiUwBijUEBAQgiQW0gsQCHUSQ3Be1dGrY0lWRWdOQ3IemDlksNATDDlR1ELc4oohB3MyIIjZQ4UdFiMV9AGwVy27LGHsmAAAAAElFTkSuQmCC',
			'E652' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHaY6IIkFNLC2sjYwBASgiIk0sjYwOoigijWwTmVoEEFyX2jUtLClmVmropDcF9Ag2gokGx3QzHNoCGhlQBNzbQiYwoDmFkZHhwB0NzOEMoaGDILwoyLE4j4AjdvNS9J6uY8AAAAASUVORK5CYII=',
			'A723' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUIdkMRYAxgaHR0dHQKQxESmMDS6NgQ0iCCJBbQytALJhgAk90UtXTVt1cqspVlI7gOqC4CoROgNDWV0YJjCgGYeawNQJZqYCNCNjChuAYmxhgaguHmgwo+KEIv7ANr4zM7Vlsj0AAAAAElFTkSuQmCC',
			'F064' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGRoCkMQCGhhDGB0dGlHFWFtZGxxaUcVEGl0bGKYEILkvNGraytSpq6KikNwHVufo6ICpNzA0BMOOAGxuQRPDdPNAhR8VIRb3AQBJBs7FZfwCjwAAAABJRU5ErkJggg==',
			'5B06' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEASCwwQaWVtCHRAdl/YtKlhS1dFpmYhu68VrA7FPKBYoytQrwiyHa0QO5DFRKZguoU1ANPNAxV+VIRY3AcAR0zMMPt2abEAAAAASUVORK5CYII=',
			'3031' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGVqRxQKmMIawNjpMRVHZygpUExCKIjZFpNGh0QGmF+yklVHTVmZNXbUUxX2o6qDmAcUaAlqx2IHNLShiUDeHBgyC8KMixOI+AIBdzII96O9xAAAAAElFTkSuQmCC',
			'7383' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUIdkEVbRVoZHR0dAlDEGBpdGwIaRJDFpjAA1Tk0BCC7L2pV2KrQVUuzkNzH6ICiDgxZGzDNE8EiBuRhuCWgAYubByj8qAixuA8AJ//MUzMdB7cAAAAASUVORK5CYII=',
			'9FDC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQ11DGaYGIImJTBFpYG10CBBBEgtoBYo1BDqwYBFDdt+0qVPDlq6KzEJ2H6srijoIbMUUE8BiBza3sAJ5rGhuHqjwoyLE4j4At1vLt8rUPXAAAAAASUVORK5CYII=',
			'7AB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGUMDkEVbGUNYGx0dUFS2srayNgSiik0RaXRtdHR1QHZf1LSVqaEro6KQ3MfoAFLn0CCCpJe1QTTUtSEARUykAagOaAeyWEADWG9AALpYKMNUh0EQflSEWNwHAELwzNSgHRYpAAAAAElFTkSuQmCC',
			'2A4A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHVqRxUSmMIYwtDpMdUASC2hlbWWY6hAQgKy7VaTRIdDRQQTZfdOmrczMzMyahuy+AJFG10a4OjBkdBANdQ0NDA1BdksD0Dw0dSJYxEJDMcUGKvyoCLG4DwCR88yewAbWaAAAAABJRU5ErkJggg==',
			'6DBA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGVqRxUSmiLSyNjpMdUASC2gRaXRtCAgIQBZrAIo1OjqIILkvMmraytTQlVnTkNwXMgVFHURvK8i8wNAQTDEUdRC3oOqFuJkRRWygwo+KEIv7ABkVzZVmh1+hAAAAAElFTkSuQmCC',
			'3360' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANYQxhCGVqRxQKmiLQyOjpMdUBW2crQ6NrgEBCALDaFoZW1gdFBBMl9K6NWhS2dujJrGrL7QOocHWHqkMwLxCIWgGIHNrdgc/NAhR8VIRb3AQCYasvGtFxyLQAAAABJRU5ErkJggg==',
			'FEA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQRNjhaiGuy80amrY0lVRQIhwH1RdI7odrKEBrQyY5k3BIhaAKiYaytoQGBoyCMKPihCL+wA/G84MUM8i1AAAAABJRU5ErkJggg==',
			'BE40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHVqRxQKmiDQwtDpMdUAWawWKTXUICEBXF+joIILkvtCoqWErMzOzpiG5D6SOtRGuDm4ea2gghhjQLZh2NKK6BZubByr8qAixuA8ApD7OK7B55yUAAAAASUVORK5CYII=',
			'1DD1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGVqRxVgdRFpZGx2mIouJOog0ujYEhKLqBYvB9IKdtDJr2srUVVFLkd2Hpo5UMZBbUMREQ8BuDg0YBOFHRYjFfQC3a8shZ8m+jQAAAABJRU5ErkJggg==',
			'2AF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0NDkMREpjCGsIJoJLGAVtZWdDGGVpFGV5AcsvumTVuZGrpqZRay+wLA6lqR7WV0EA0Fik1BcUsDWF0AspgIWIzRAVksNBRTbKDCj4oQi/sAFSDLTeiKhdcAAAAASUVORK5CYII=',
			'C180' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGVqRxURaGQMYHR2mOiCJBTSyBrA2BAQEIIs1MADVOTqIILkvCoRCV2ZNQ3Ifmjq4GGtDIKpYIwOGHSKtDBhuYQ1hDUV380CFHxUhFvcBAFd4yfVMhz6RAAAAAElFTkSuQmCC',
			'43C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37prCGMIQ6tKKIhYi0MjoETHVAEmMMYWh0bRAICEASY53C0MrawOggguS+adNWhS1dtTJrGpL7AlDVgWFoKMg8VDGGKZh2MEzBdAtWNw9U+FEPYnEfAHFuy7gg6mvoAAAAAElFTkSuQmCC',
			'7703' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMIQ6IIu2MjQ6hDI6BKCJOTo6NIggi01haGVtCGgIQHZf1KppS1dFLc1Cch+jA0MAkjowZAWKgsSQzRMBiaLZAVaB5hawGLqbByj8qAixuA8AjQPMdeHLZ1sAAAAASUVORK5CYII=',
			'D8FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA0NDkMQCprC2sjYwOiCrC2gVaXTFEENRB3ZS1NKVYUtDV4ZmIbkPTR0e87CIYXEL2M1oYgMVflSEWNwHAJDeyrsSl40XAAAAAElFTkSuQmCC',
			'0130' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGVqRxVgDGANYGx2mOiCJiUxhDWBoCAgIQBILaGUIYGh0dBBBcl/U0lVRq6auzJqG5D40dQixhkAUMZEpDBh2AG3FcAujA2soupsHKvyoCLG4DwCheMouhp+b2AAAAABJRU5ErkJggg==',
			'600C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEwNQBITmcIYwhDKECCCJBbQwtrK6OjowIIs1iDS6NoQ6IDsvsioaStTV0VmIbsvZAqKOojeVmximHZgcws2Nw9U+FERYnEfAKNPyvp7rZQRAAAAAElFTkSuQmCC',
			'805E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMDkMREpjCGsDYwOiCrC2hlbUUXE5ki0ug6FS4GdtLSqGkrUzMzQ7OQ3AdS59AQiGYeNjGQHYFodjCGMDo6ooiB3MwQyoji5oEKPypCLO4DAB+bycKYiCf7AAAAAElFTkSuQmCC',
			'1D69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxOoi0Mjo6BAQgiYk6iDS6Njg6iKDoBYkxwsTATlqZNW1l6tRVUWFI7gOrc3SYiqk3oAGLGLodmG4JwXTzQIUfFSEW9wEAOh3J9ApLrXYAAAAASUVORK5CYII=',
			'139D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGUMdkMRYHURaGR0dHQKQxEQdGBpdGwIdRFD0MrSyIsTATlqZtSpsZWZk1jQk94HUMYRg6G10wDSv0RFDDItbQjDdPFDhR0WIxX0A+HPIF2g6R2IAAAAASUVORK5CYII=',
			'C404' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYWhmmMDQEIImJtDJMZQhlaEQWC2hkCGV0dGhFEWtgdGVtCJgSgOS+qFVLly5dFRUVheS+AKCJrA2BDqh6RUNdGwJDQ1DtaAXage6WVqBbUMSwuXmgwo+KEIv7ABxOzc0KITdZAAAAAElFTkSuQmCC',
			'1E33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1EQxlDGUIdkMRYHUQaWBsdHQKQxESBYgwNAQ0iKHqBvEaHhgAk963Mmhq2auqqpVlI7kNThxDDZh4WMQy3hGC6eaDCj4oQi/sA+fTKfAlhpNcAAAAASUVORK5CYII=',
			'5696' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0sjYEOgggiQUGiDSAxJDdFzZtWtjKzMjULGT3tYq2MoQEopjH0CrS6ADUK4JsB1DMEU1MZAqmW1gDMN08UOFHRYjFfQCcVcuyXoTyrQAAAABJRU5ErkJggg==',
			'DCB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGVqRxQKmsDa6NjpMRRFrFWlwbQgIRRdjbXSA6QU7KWrptFVLQ1ctRXYfmjqEGJDEYgc2t6CIQd0cGjAIwo+KEIv7AANIz0zXQBCdAAAAAElFTkSuQmCC',
			'B6D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaWRtCHQQQFEn0gASQ3ZfaNS0sKWrIlOzkNwXMEW0FagOwzxXoF4RQmJY3ILNzQMVflSEWNwHAGd/zhGrPfOTAAAAAElFTkSuQmCC',
			'66F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZW1gCAhAEgtoEWlkbWB0EEEWaxBpQBIDOykyalrY0tBVUWFI7guZIgoybyqK3laRRleQXZhiKHZgcwvYzUDzkN08UOFHRYjFfQBL78uQBCh5kAAAAABJRU5ErkJggg==',
			'DEC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQxlCHUIdkMQCpog0MDoEOgQgi7WKNLA2CDSIYIgBaST3RS2dGrZ01aqlWUjuQ1OHIoZpHpodWNyCzc0DFX5UhFjcBwB+uM4Fw6xAoQAAAABJRU5ErkJggg==',
			'4284' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM3QMQ7AIAgFUBy4QXsfHbrTRBZPg4M3IN6gi6esI9qObVrYfiC8AO1SAn/qd3zqIjAI2SxiccFnm7m45E2o2AwVcgheyfhqbUfjlpLxkfYrfdDuMgOh7BxHi8cuGSyK0i1TtrKfzV/977m+8Z1kCs1C4JEuNAAAAABJRU5ErkJggg==',
			'9D40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxgaHVqRxUSmiLQytDpMdUASC2gVaQSKBASgiwU6OogguW/a1GkrMzMzs6YhuY/VVaTRtRGuDgKBel1DA1HEBEDmNaLaAXZLI6pbsLl5oMKPihCL+wApp823wifiLAAAAABJRU5ErkJggg==',
			'18DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUMdkMRYHVhbWRsdHQKQxEQdRBpdGwIdRFD0AtUhxMBOWpm1MmzpqsisaUjuQ1MHFcNmHg470N0SgunmgQo/KkIs7gMAZv7JP6TE56AAAAAASUVORK5CYII=',
			'A3A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB1YQximMEx1QBJjDRBpZQhlCAhAEhOZwtDo6OjoIIIkFtDK0MraEAgTAzspaumqsKWroqLCkNwHURcwFVlvaChDo2toQAOaeY2uDQFodoiA9KK4JaCVNQRkHrKbByr8qAixuA8A0t/NSPOp2PEAAAAASUVORK5CYII=',
			'3DCC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7RANEQxhCHaYGIIkFTBFpZXQICBBBVtkq0ujaIOjAgiw2BSTG6IDsvpVR01amrlqZheI+VHVI5mETQ7UDm1uwuXmgwo+KEIv7AJiHy7M9EsCfAAAAAElFTkSuQmCC',
			'E960' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGVqRxQIaWFsZHR2mOqCIiTS6NjgEBGCIMTqIILkvNGrp0tSpK7OmIbkvoIEx0NXREaYOKsYA1BuIJsYCFAtAswPTLdjcPFDhR0WIxX0A0Q7NhOKjZuIAAAAASUVORK5CYII=',
			'23F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANYQ1hDA0NDkMREpoi0soJoJLGAVoZGVzQxhlYGsLoAZPdNWxW2NHTVyixk9wWA1bUi28voADZvCopbGsBiAchiIg0gtwBVI4mFhgLdjCY2UOFHRYjFfQBoCMp928D73wAAAABJRU5ErkJggg==',
			'394A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYQxgaHVqRxQKmsLYytDpMdUBW2SrSCBQJCEAWmwIUC3R0EEFy38qopUszMzOzpiG7bwpjoGsjXB3UPIZG19DA0BAUMZZGBzR1YLegiUHcjGbeAIUfFSEW9wEA2IXMiLIHu4gAAAAASUVORK5CYII=',
			'AD9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgDRFoZHR2mOiCJiUwRaXRtCAgIQBILaAWJBTqIILkvaum0lZmZkVnTkNwHUucQAlcHhqGhQLGGwNAQNPMcG1DVAcWAbnFEEwO5mRFFbKDCj4oQi/sA32zM3Ftyz30AAAAASUVORK5CYII=',
			'40A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37pjAEAPFUB2SxEMYQhlCGgAAkMcYQ1lZGR0cHASQx1ikija4NgQ7I7ps2bdrK1FWRqVlI7guAqEMxLzQUKBYa6CCC4hbWVtYGdDGgzQ0BKHpBbgaKobp5oMKPehCL+wBmNcvvxnWfRQAAAABJRU5ErkJggg==',
			'4C47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjCGMjQ6hoYgi4WwNjq0OjSIIIkxhog0OExFFWOdAuQFOjQEILlv2rRpq1ZmZq3MQnJfAFAdyERke0NDgWKhAVNQ3QK0o9EhAFUMqLPR0QGLm1HFBir8qAexuA8Al8bNLm/2IgsAAAAASUVORK5CYII=',
			'3F66' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RANEQx1CGaY6IIkFTBFpYHR0CAhAVtkq0sDa4OgggCw2BSTG6IDsvpVRU8OWTl2ZmoXsPpA6R0cs5gU6iBAQw+YW0QCgCjQ3D1T4URFicR8AMG/LbVWjDMoAAAAASUVORK5CYII=',
			'5C58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDHaY6IIkFNLA2ujYwBASgiIk0uDYwOoggiQUGiDSwToWrAzspbNq0VUszs6ZmIbuvFaQrAMU8iFgginkBrSA7UMVEprA2Ojo6oOhlDWAMZQhlQHHzQIUfFSEW9wEAZovNA/wiUtEAAAAASUVORK5CYII=',
			'76A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMIaGIIu2srYyhDI0iKCIiTQyOjqgik0RaWBtCABCJPdFTQtbuipqZRaS+xgdRFuB6lqR7WVtEGl0DQ2YgiwmAhJrCAhAFgtoYAXqDXRAFWMMQRcbqPCjIsTiPgD9kcwv7SZPGQAAAABJRU5ErkJggg==',
			'EF84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGRoCkMQCGkQaGB0dGtHFWBsCWrGomxKA5L7QqKlhq0JXRUUhuQ+iztEB07zA0BBMO7C5BUUsNESkgQHNzQMVflSEWNwHAF+xzsSfxIPkAAAAAElFTkSuQmCC',
			'6223' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM2QsQ2AMAwE30U2CPuYgt4FKWAEpjAS2SArUJApSWkLShD4pS+ueJ2MejnFn/KKXxAakZDYsFhCpr5nMUy2uA4qGi1TrNxajN80170ey74Yv7GgIEPdXoY06vcycaOONRclJucSpEtDEuf81f8ezI3fCb8yzIqjhK1RAAAAAElFTkSuQmCC',
			'5770' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA1qRxQIaGBodGgKmOmCKBQQgiQUGMLQyNDo6iCC5L2zaqmmrlq7MmobsvlaGAIYpjDB1UDFGB4YAVLGAVtYGkCiyHSJTRBpYGxhQ3MIaABZDcfNAhR8VIRb3AQD7zcxR5s9dLwAAAABJRU5ErkJggg==',
			'E550' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHVqRxQIaRBpYGximOmCKBQSgioWwTmV0EEFyX2jU1KVLMzOzpiG5D6in0aEhEKYOj5hIo2tDAJodrK2Mjg4obgkNYQxhCGVAcfNAhR8VIRb3AQCTXc1aVIVpswAAAABJRU5ErkJggg==',
			'367E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDA0MDkMQCprC2MjQEOqCobBVpxBCbItLA0OgIEwM7aWXUtLBVS1eGZiG7b4poK8MURgzzHAIwxRwdUMVAbmFtQBUDu7mBEcXNAxV+VIRY3AcATjXJssJmffMAAAAASUVORK5CYII=',
			'4272' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QMQ6AMAgA6cAP6n9Y3DEpi6+hQ39Q+wOXvlLcqDpqIrddSLgA/TYKf+KbvhoSCm/kXcICyszOhRQz6ULROayQyWx0fa313eir6+MKJ9nfEAE2yqWFAtnm4FBRbXNwk8waJP3hf+/x0HcAYGzMGDX4fLUAAAAASUVORK5CYII=',
			'FE5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHUMDkMQCGkQaWBsYHRiIEZsKFwM7KTRqatjSzMzQLCT3gdQxNARi6MUmxopFjNHREU1MNJQhlBHFzQMVflSEWNwHAFnwysJurB7dAAAAAElFTkSuQmCC',
			'C2CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCHVqRxURaWVsZHQKmOiCJBTSKNLo2CAQEIIs1MADFGB1EkNwXtWrV0qWrVmZNQ3IfUN0UVoQ6mFgAUCw0BMUORgfWBkEUdUC3AHUGooixhoiGOoQ6oogNVPhREWJxHwDPxMun/iLfBgAAAABJRU5ErkJggg==',
			'DCAA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMLQiiwVMYW10CGWY6oAs1irS4OjoEBCAJsbaEOggguS+qKXTVi1dFZk1Dcl9aOoQYqGBoSFoYq7o6oBuQRcDuRndvIEKPypCLO4DACJszpSRO87EAAAAAElFTkSuQmCC',
			'3336' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7RANYQxhDGaY6IIkFTBFpZW10CAhAVtnK0OjQEOgggCw2BSTq6IDsvpVRq8JWTV2ZmoXsPog6rOaJEBDD5hZsbh6o8KMixOI+AG0xzHCZPNTsAAAAAElFTkSuQmCC',
			'246F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUNDkMREpjBMZXR0dEBWFwBUxdqAKsbQyujK2sAIE4O4adrSpUunrgzNQnZfgEgrK5p5jA6ioa4NgShirEATWdHEREC2oOkNDQW7GdUtAxR+VIRY3AcAqjfIg6JQmPEAAAAASUVORK5CYII=',
			'1FA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMEx1QBJjdRBpYAhlCAhAEhMFijE6OoJkkPSKNLA2BDSIILlvZdbUsKWrooAQ4T6oukYHdL2hAa0MmOZNwSIWgCwmGgISCwwNGQThR0WIxX0AfbzKOYSXVscAAAAASUVORK5CYII=',
			'FDF0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA1qRxQIaRFpZGximOqCKNbo2MAQEYIgxOogguS80atrK1NCVWdOQ3IemjoAYhh1Y3AJ0cwMDipsHKvyoCLG4DwAWxM24sHoXEAAAAABJRU5ErkJggg==',
			'D7B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGaY6IIkFTGFodG10CAhAFmsFijUEOoigirWyNjrCxMBOilq6atrS0FVRYUjuA6oLYG10mIqql9GBtSGgAVWMtQEohmrHFJEGVjS3hAYAxdDcPFDhR0WIxX0AJR7OeHSpG+AAAAAASUVORK5CYII=',
			'A931' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGVqRxVgDWFtZGx2mIouJTBFpdGgICEUWC2gFijU6wPSCnRS1dOnSrKmrliK7L6CVMRBJHRiGhjKAzGtFNY8FixjYLWhiYDeHBgyC8KMixOI+APuUzfQGfyN2AAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>