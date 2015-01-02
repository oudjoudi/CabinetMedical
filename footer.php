<?php

include('config/config.inc.php');

?>

</div>

<script src="<?php echo $siteurl; ?>/public/js/jquery-2.1.3.min.js"></script>
<script src="<?php echo $siteurl; ?>/public/js/bootstrap.min.js"></script>
<script src="<?php echo $siteurl; ?>/public/js/moment-with-locales.min.js"></script>

<script src="<?php echo $siteurl; ?>/public/js/bootstrap-datetimepicker.min.js"></script>

<script src="<?php echo $siteurl; ?>/public/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $siteurl; ?>/public/js/bootstrap-datepicker.fr.min.js"></script>


<script type="application/javascript">
    var url = window.location;
    $('ul.nav a[href="'+ url +'"]').parent().addClass('active');

    $('ul.nav a').filter(function() {
        return this.href == url;
    }).parent().addClass('active');
</script>
<script type="application/javascript">
    $('#dateNaissance').datepicker({
        format: "dd/mm/yyyy",
        endDate: "0d",
        language: "fr",
        todayHighlight: true,
        startView: 2
    });
    $('#dateConsult').datepicker({
        format: "dd/mm/yyyy",
        startDate: "0d",
        language: "fr",
        todayHighlight: true
    });
    $('.datetimepicker').datetimepicker({
        language: 'fr',
        pickDate: false,
        minuteStepping: 5,
        useSeconds: false
    });
</script>
<!-- Si on utilise pas cette fonction, le formulaire est réinitialiser (valeurs par défaut) et non vider (champs vides) -->
<script type="application/javascript">
    $('form button:reset').click(function () {
        $('form')
            .find(':radio, :checkbox').removeAttr('checked').end()
            .find('textarea, :text, select').val('')
        return false;
    });
</script>
<script type="application/javascript">
    $("#selectUsagers").change(function () {
        $('#selectMedecins')
            .empty()
            .append('<option selected="selected" value="null">Recherche en cours...</option>')
        ;
        $.post(
            'recupererMedecin.php',
            {
                idUsager: $("#selectUsagers").val()
            },
            function(data){
                if(data.statut == "success"){
                    $('#selectMedecins').empty();
                    $.grep(data.medecins, function(e){
                        if(e.id == data.traitant){
                            $('#selectMedecins').append($('<option>', {
                                value: e.id,
                                text : e.civilite + ' ' + e.nom + ' ' + e.prenom
                            }));
                            $('#selectMedecins').append('<option disabled>──────────</option>');
                        }
                    });
                    $.each(data.medecins, function (i, item) {
                        if(item.id != data.traitant) {
                            $('#selectMedecins').append($('<option>', {
                                value: item.id,
                                text: item.civilite + ' ' + item.nom + ' ' + item.prenom
                            }));
                        }
                    });
                } else {
                    alert("Erreur lors de la récupération des médecins : " + data.error);
                }
                console.log(data);
            },
            'json'
        );
    });
</script>
</body>
</html>