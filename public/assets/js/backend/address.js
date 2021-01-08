
//绑定事件
$("#province_id").change(function () {
	let id = $(this).val()
	console.log(id)
	let name = $(this).find("option:selected").text()
	$('#c-province').val(name)
	addressBind(id, 2);
})

$("#city_id").change(function () {
	let id = $(this).val()
	let name = $(this).find("option:selected").text()
	$('#c-city').val(name)
	addressBind(id, 3);
})
$("#area_id").change(function () {
	let name = $(this).find("option:selected").text()
	$('#c-area').val(name)
})
//默认绑定省
let province = $("#province_id").val()
let city = $("#city_id").val()
let area = $("#area_id").val()
if(province){
	addressBind(0,1)
	$("#province_id").trigger('change')
	$("#city_id").trigger('change');
} else {
	addressBind(0,1)
}
function Bind(str) {
	alert($("#Province").html());
	$("#Province").val(str);
}
function addressBind(id, type) {
	//清空下拉数据
	
	if(type == 1) {
		$("#province_id").html("");
	} else if(type == 2) {
		$("#city_id").html("");
	} else {
		$("#area_id").html("");
	}
	var str = "<option>==请选择===</option>";
	$.ajax({
		type: "get",
		url: "/admin/area/linkagelist",
		data: {"pid": id},
		dataType: "json",
		async: false,
		success: function (res) {
			//从服务器获取数据进行绑定
			console.log(res)
			// res = JSON.parse(res)

			if(res.code == 200) {
				$.each(res.data, function (i, item) {
					if(province == item.name || city == item.name || area == item.name) {
						str += "<option value=" + item.id + " selected>" + item.name + "</option>";
					} else {
						str += "<option value=" + item.id + ">" + item.name + "</option>";
					}
				})
			} else {
				alert('没有数据')
			}
			//将数据添加到省份这个下拉框里面
			if(type == 1) {
				$("#province_id").append(str);
			} else if(type == 2) {
				$("#city_id").append(str);
			} else {
				$("#area_id").append(str);
			}
		},
		error: function () { alert("Error"); }
	});
}
