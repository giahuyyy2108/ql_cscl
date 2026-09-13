$(function(){
 var root=$('.danhmuc-crud'), modal=$('.modal-danhmuc'), form=$('.form-danhmuc');
 if(!root.length)return;
 var route=root.data('route'), label=root.data('label');
 function msg(title,text,type){if(typeof Swal!=='undefined')Swal.fire(title,text,type);else alert(text);}
 function fill(fields){form[0].reset();$.each(fields||{},function(name,value){form.find('[name="'+name+'"]').val(value);});}
 root.on('click','.btn-them-danhmuc',function(){fill({id:0,is_new:1});form.find('[name=id][type=number]').val('');form.find('[name=id]').prop('readonly',false);modal.find('.modal-title').text('Thêm '+label);modal.modal('show');});
 root.on('click','.btn-sua-danhmuc',function(){fill($(this).data('fields'));form.find('[name=id][type=number]').prop('readonly',true);modal.find('.modal-title').text('Sửa '+label);modal.modal('show');});
 modal.on('shown.bs.modal',function(){form.find('input:visible:first').focus();});
 form.on('submit',function(e){e.preventDefault();var button=form.find('.btn-luu-danhmuc').prop('disabled',true);$.ajax({url:$('#ULocal').val()+route+'/save/',type:'POST',dataType:'json',data:form.serialize(),success:function(r){if(r&&r.success)location.reload();else msg('Không thể lưu',(r&&r.message)||'Có lỗi xảy ra.','error');},error:function(){msg('Lỗi','Không thể kết nối đến máy chủ.','error');},complete:function(){button.prop('disabled',false);}});});
 root.on('click','.btn-xoa-danhmuc',function(){var id=$(this).data('id'),ten=$(this).data('ten');function remove(){ $.ajax({url:$('#ULocal').val()+route+'/delete/',type:'POST',dataType:'json',data:{id:id},success:function(r){if(r&&r.success)location.reload();else msg('Không thể xóa',(r&&r.message)||'Có lỗi xảy ra.','error');},error:function(){msg('Lỗi','Không thể kết nối đến máy chủ.','error');}}); } if(typeof Swal!=='undefined'){Swal.fire({title:'Xóa '+label+'?',text:'Bạn có chắc muốn xóa “'+ten+'”?',icon:'warning',showCancelButton:true,confirmButtonText:'Xóa',cancelButtonText:'Hủy'}).then(function(r){if(r.isConfirmed||r.value)remove();});}else if(confirm('Bạn có chắc muốn xóa "'+ten+'"?'))remove();});
});
