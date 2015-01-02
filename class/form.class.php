<?php

/**
 * Class Form
 *
 * Créer un formulaire HTML
 */
class Form
{

    /**
     * @var null|string Stocke la chaine de caractère complète du formulaire
     */
    private $form = null;

    /**
     * Créer un formulaire HTML
     * @param string $legend Balise legend HTML
     * @param string $method Méthode d'envoi du formulaire (POST ou GET)
     * @param string $dest URL d'envoi du formulaire
     */
    public function __construct($legend, $method = "post", $dest = null)
    {
        $this->form = ' <form action="' . $dest . '" method="' . $method . '" class="form-horizontal" role="form">
						  <fieldset>
							<legend>' . $legend . '</legend>';
    }

    /**
     * Créer un champ textarea
     * @param string $label Label du champ
     * @param string $name Nom du champ
     * @param int    $ligne Nombre de ligne du champ
     * @param int    $colonne Nombre de colonne du champ
     */
    public function setText($label, $name, $ligne = 4, $colonne = 50)
    {
        $this->form .= '<div class="form-group"><label for="' . $name . '">' . $label . '</label><textarea rows="' . $ligne . '" cols="' . $colonne . '"></textarea><br /></div>';
    }

    /**
     * Créer un champ de type input
     * @param string $label Label du champ
     * @param string $name Nom du champ
     * @param string $type Type de champ
     * @param string $value Valeur du champ
     * @param string $option Options du champ
     */
    public function setInput($label, $name, $type = "text", $value = null, $option = null)
    {
        if ($label == null) {
            $this->form .= '<div class="form-group"><input type="' . $type . '" name="' . $name . '" value="' . $value . '" ' . $option . ' ></div>';
        } else {
            $this->form .= '<div class="form-group"><label for="' . $name . '" class="col-sm-2 control-label">' . $label . '</label><div class="col-sm-10"><input type="' . $type . '" name="' . $name . '" value="' . $value . '" ' . $option . ' ></div></div>';
        }

    }

    /**
     * Créer un champ de type combo
     * @param string $label       Label du champ
     * @param string $name        Nom du champ
     * @param array  $options     Tableau d'options avec une valeur et un libellé
     * @param int    $prefered    Indice du tableau pour le premier choix
     * @param string $optionsHTML Options HTML
     */
    public function setSelect($label, $name, array $options, $prefered = null, $optionsHTML = null)
    {
        $this->form .= '<div class="form-group"><label for="' . $name . '" class="col-sm-2 control-label">' . $label . '</label><div class="col-sm-10"><select name="' . $name . '" ' . $optionsHTML .'>';
        if ($prefered != null) {
            $this->form .= '<option value="' . $prefered . '">' . $options[$prefered] . '</option>';
            $this->form .= '<option disabled>──────────</option>';
            unset($options[$prefered]);
        }
        foreach ($options as $cle => $option) {
            $this->form .= '<option value="' . $cle . '">' . $option . '</option>';
        }
        $this->form .= '</select></div></div>';
    }

    /**
     * Créer un bouton de type button
     * @param string $label Label du bouton
     * @param string $name Nom du bouton
     * @param string $type Type du bouton
     * @param string $class Classes du bouton
     */
    public function setButton($label, $name, $type="submit", $class = "btn btn-default")
    {
        $this->form .= '<button name="' . $name . '" type="' . $type . '" class="'. $class .'">' . $label . '</button> ';
    }

    /**
     * Récupérer le formulaire final
     * @return string Formulaire complet
     */
    public function getForm()
    {
        return $this->form . ' </fieldset>
						</form>';
    }

}