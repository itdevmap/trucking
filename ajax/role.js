
function ReadData(hal) {
	var search_name = $("#search_name").val();
    $.get("ajax/role_crud.php", {search_name:search_name, hal:hal, type:"read" }, function (data, status) {
        $(".tampil_data").html(data);
		$("#hal").val(hal);
    });
}

function GetData(id) {
    $("#id").val(id);
    $.post("ajax/role_crud.php", {
            id: id, type:"detil"
        },
        function (data, status) {
            var data = JSON.parse(data);
			$("#nama_cabang").val(data.nama_cabang);
			$("#pimpinan").val(data.pimpinan);
			$("#alamat").val(data.alamat);
			$("#telp").val(data.telp);
			$("#email").val(data.email);
			$("#stat").val(data.status);
			$("#mode").val('Edit');			
        }
    );
    $("#Data").modal("show");
}
function add() {	
	var email = $("#email").val();
	if(!$("#nama_cabang").val()){
        alert("Nama Cabang harus diisi !..");
    }
	else if(!$("#pimpinan").val()){
        alert("Pimpinan Cabang harus diisi !..");
    }
	else if(!$("#alamat").val()){
        alert("Alamat harus diisi !..");
    }
	else if(!$("#telp").val()){
        alert("No. Telp harus diisi !..");
	}
	else if(!email.match(/\S/) || !email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) {
		alert ('Alamat email tidak valid..');	
	}
	else
	{
		var r = confirm("Are you sure ?...");
		if (r == true) {	
			var id = $("#id").val();
			var nama_cabang = $("#nama_cabang").val();
			var pimpinan = $("#pimpinan").val();
			var alamat = $("#alamat").val();
			var telp = $("#telp").val();	
			var stat = $("#stat").val();
			var mode = $("#mode").val();
			var hal = $("#hal").val();
			$.post("ajax/role_crud.php", {
				id:id,
				nama_cabang:nama_cabang,
				pimpinan:pimpinan,
				alamat:alamat,
				telp:telp,
				stat:stat,
				email:email,
				stat:stat,
				mode:mode,
				type : "add"
				}, function (data, status) {
				alert(data);
				if(data == 'Data saved!')
				{
					$("#Data").modal("hide");				
					ReadData(hal);
					$("#nama_cabang").val('');
					$("#pimpinan").val('');
					$("#alamat").val('');
					$("#telp").val('');
					$("#email").val('');
				}				
			});
		}
	}	
}


function GetPhoto(id) {
    $("#idx").val(id);
    $.post("ajax/user_crud.php", {
            id: id, type:"detil"
        },
        function (data, status) {
            var data = JSON.parse(data);
			$("#photo_lama").val(data.photo);
			$("#mode").val('Edit');
			
        }
    );
    // Open modal popup
    $("#Add_Photo").modal("show");
}


$(document).ready(function (e) {
	$("#formx").on('submit',(function(e) {
		e.preventDefault();
		var id = $("#idx").val();
		var hal = $("#hal").val();
		$.ajax({
        	url: "ajax/upload_user.php",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			beforeSend : function()
			{				
				$("#err").fadeOut();
			},
			success: function(data)
		    {
				
				if(data=='invalid')
				{
					// invalid file format.
					$("#err").html("Invalid File !").fadeIn();
				}
				else
				{
					// view uploaded file.
					//$("#preview").html(data).fadeIn();
				
					$("#formx")[0].reset();	
					$("#Add_Photo").modal("hide");
					ReadData(hal);
				}
		    },
		  	error: function(e) 
	    	{
				$("#err").html(e).fadeIn();
	    	} 	        
	   });
	}));
});


function changeDateFormat(inputDate){
    var splitDate = inputDate.split('-');
    if(splitDate.count == 0){
        return null;
    }
    var year = splitDate[0];
    var month = splitDate[2];
    var day = splitDate[1]; 
    return month + '-' + day + '-' + year;
}

$(document).ready(function () {
	var hal = $("#hal").val();
	ReadData(hal);
});




