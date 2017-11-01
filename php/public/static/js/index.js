/*!
 * App Javascript v1.1.1
 * Author: Smile
 * Date: 2017-09-25
 */

//解决移动端300毫秒延迟
 window.addEventListener('load', function() {
   FastClick.attach(document.body);
 }, false);

$(function(){

  //公用Tab切换
  $(".j-tab li").click(function(){

    $(this).addClass("on").siblings().removeClass("on");

     var index = $(".j-tab>li").index($(this));

     $(".j-tab-main>.item:eq("+index+")").show().siblings('.item').hide();

  });
 

})
