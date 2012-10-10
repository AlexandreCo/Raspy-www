<?php # coding: utf-8

/** Liste les plugins installés
 * Accès via : ?action=list
 */
class ListPlugins
{
   public $description = "Affiche la liste des plugins chargés";
      
   function action($a)
   {
      global $plugins,$CONTENT,$PAGE_TITLE,$PAGE_TITLE_link,$editable;
      
      if ($a == "list")
      {
         $CONTENT = ''; // reset du contenu de la page
         $PAGE_TITLE_link = FALSE; // pas de lien sur le titre
         $editable = FALSE; // non editable
         $PAGE_TITLE = "Liste des plugins"; // titre de la page
         // remplissage du contenu
         foreach ($plugins as $p)
            $CONTENT .= get_class($p) . " : ". $p->description ."<br/>\n";
         return TRUE;
      }
      return FALSE; // action non traitée
   } // action
}

?>
