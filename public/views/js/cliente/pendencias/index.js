/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){    
    $("#todos").click(function() {
        var checked = $(".pendencias").prop("checked");
        if (checked) {
            $(".pendencias").prop("checked", false);
        } else {
            $(".pendencias").prop("checked", true);               
        }       
    });
});

