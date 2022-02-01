$(function (){
	
	const panels = document.querySelectorAll('.graphic');
    //console.log(panels);
    if (panels!=null) {

        panels.forEach(panel => {
            const context = panel.querySelector('canvas');
            const url = panel.getAttribute('data-config');//l'url vers la configuration du chart sur le serveur
            panel.classList.add('graphic-lodding');

            $.getJSON (url, (data) => {
                //console.log(data);
                panel.classList.remove('graphic-lodding');
                new Chart(context, data.chart.config);
            }).fail(function (ev) {
                panel.classList.remove('graphic-lodding');
                const data = ev.responseText;
                //console.log(data);
                panel.querySelector('canvas').parentNode.innerHTML = "<div class='alert alert-care alert-danger'><pre>"+data+"</pre></div>";
            });
            
        });
    }
});