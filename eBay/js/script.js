$(document).ready(function(){
$("#submit").click(function(){
var username = $("#username").val();
var userpassword = $("#userpassword").val();
var fullname = $("#fullname").val();
var email = $("#email").val();
var phone = $("#phone").val();
var address = $("#address").val();
var lastlogin = $("#lastLogin").val();
var location = $("#location").val();

var dataString = 'username='+ username + '&userpassword='+
userpassword + '&fullname='+ fullname + '&email='+ email+ '&phone='+
phone +'&address='+ address + '&lastlogin='+ lastlogin + '&location='+
location;
if(username==''||email=='')
{
alert("Please Fill All Fields");
}
else
{
$.ajax({
type: "POST",
url: "register.php",
data: dataString,
cache: false,
success: function(result){
alert(result);
}
});
}
return false;
});
});