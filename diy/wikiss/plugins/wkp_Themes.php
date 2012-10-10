<?php # coding: utf-8

/** Gestion de plusieurs thèmes
 * balises à ajouter au template.html :
 *  - {THEME_LIST} : le selecteur de thèmes
 * action : 
 *  - theme : selectionner un thème et installer le cookie
 */
class Themes
{
   public $description = "Permet de choisir un template pour WiKiss";
   const theme_dir = "themes/";
   private $selector = "";
   
   function __construct()
   {
      global $template,$START_PAGE;
      
      if (isset($_COOKIE["WiKissTheme"]) && $_COOKIE["WiKissTheme"] != "template")
         $current = $_COOKIE["WiKissTheme"];
      else
         $current = "defaut";
      $template = Themes::theme_dir . $current.".html";
      
      if (is_dir(self::theme_dir) && ($dir = opendir(self::theme_dir)))
      {
         $this->selector .= '<select id="themes" onchange=\'javascript: sel=document.getElementById("themes");window.location.replace("./?page='.(isset($_GET['page'])?urlencode($_GET['page']):$START_PAGE).'&amp;action=theme&amp;t="+sel.options[sel.options.selectedIndex].text)\'>';
         while (($file = readdir($dir)) !== false)
         {
            if (preg_match("/(.+)\.html$/", $file, $matches)>0)
            {
               if ($matches[1] == $current)
                  $this->selector .= "<option selected='selected'>".$matches[1]."</option>";
               else
                  $this->selector .= "<option>".$matches[1]."</option>";
            }
         }
         $this->selector .= "</select>";
      }
      else
         $template = "template.html";
   } // __construct()
      
   function action($a)
   {
      if ($a == "theme")
      {
         setcookie('WiKissTheme',$_GET['t'], time() + 365*24*3600);
         header("location: ./?page=".urlencode($_GET['page']));
         return TRUE; // Jamais
      }
      return FALSE; // action non traitée
   } // action()
   
   function template()
   {
      global $html;
      $html = str_replace('{THEME_LIST}',$this->selector,$html);
      return FALSE;
   } // template()
}

?>
