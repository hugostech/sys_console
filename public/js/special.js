/**
 * Created by Hugo on 16/2/4.
 */
function finishWarranty(url){
    if(confirm('Are you sure want to finish this ticket?')){
        window.location.href=url;
    }
}

function goBack(){
    window.history.back(-1);
}

function step3(id){
    $('#model_id_step3').val(id);
}

function detemineType(type){
    $('#type').val(type);
}

function selfStorage(){
    //$('#client_name').var('Roctech');
    alert('as');
}

function print(){
	window.print();
}
