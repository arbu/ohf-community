!function(t){function a(n){if(e[n])return e[n].exports;var p=e[n]={i:n,l:!1,exports:{}};return t[n].call(p.exports,p,p.exports,a),p.l=!0,p.exports}var e={};a.m=t,a.c=e,a.i=function(t){return t},a.d=function(t,e,n){a.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:n})},a.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return a.d(e,"a",e),e},a.o=function(t,a){return Object.prototype.hasOwnProperty.call(t,a)},a.p="",a(a.s=242)}({13:function(t,a){function e(t,a,e){for(t.empty(),a.current_page>1?t.append(n("&laquo;",1,null,e)):t.append(n("&laquo;",null,"disabled",e)),a.current_page>1?t.append(n("&lsaquo;",a.current_page-1,null,e)):t.append(n("&lsaquo;",null,"disabled",e)),i=2+Math.max(2-(a.last_page-a.current_page),0);i>=1;i--)a.current_page>i&&t.append(n(a.current_page-i,a.current_page-i,null,e));for(t.append(n(a.current_page,null,"active",e)),i=1;i<=2+Math.max(0,3-a.current_page);i++)a.current_page+i-1<a.last_page&&t.append(n(a.current_page+i,a.current_page+i,null,e));a.current_page<a.last_page?t.append(n("&rsaquo;",a.current_page+1,null,e)):t.append(n("&rsaquo;",null,"disabled",e)),a.current_page<a.last_page?t.append(n("&raquo;",a.last_page,null,e)):t.append(n("&raquo;",null,"disabled",e))}function n(t,a,e,n){var p=$("<li>").addClass("page-item");return null!=a?p.append($("<a>").addClass("page-link").attr("href","javascript:;").html(t).on("click",function(){n(a)})):p.append($("<span>").addClass("page-link").html(t)),null!=e&&p.addClass(e),p}t.exports={updatePagination:e}},145:function(t,a,e){function n(t){$("#filter-status").html("");var a=$("#results-table tbody");a.empty(),a.append($("<tr>").append($("<td>").text("Searching...").attr("colspan",10)));var e=$("#paginator");e.empty();var r=$("#paginator-info");r.empty(),$.post(filterUrl,{_token:csrfToken,family_name:$('#filter input[name="family_name"]').val(),name:$('#filter input[name="name"]').val(),case_no:$('#filter input[name="case_no"]').val(),medical_no:$('#filter input[name="medical_no"]').val(),registration_no:$('#filter input[name="registration_no"]').val(),section_card_no:$('#filter input[name="section_card_no"]').val(),temp_no:$('#filter input[name="temp_no"]').val(),nationality:$('#filter input[name="nationality"]').val(),languages:$('#filter input[name="languages"]').val(),skills:$('#filter input[name="skills"]').val(),remarks:$('#filter input[name="remarks"]').val(),page:t},function(t){a.empty(),t.data.length>0?($.each(t.data,function(t,e){a.append(p(e))}),pagination.updatePagination(e,t,n),r.html(t.from+" - "+t.to+" of "+t.total)):a.append($("<tr>").addClass("warning").append($("<td>").text("No results").attr("colspan",10)))}).fail(function(t,e){a.empty(),a.append($("<tr>").addClass("danger").append($("<td>").text(e).attr("colspan",10)))})}function p(t){return $("<tr>").attr("id","person-"+t.id).append($("<td>").append($("<a>").attr("href","people/"+t.id).text(t.family_name))).append($("<td>").append($("<a>").attr("href","people/"+t.id).text(t.name))).append($("<td>").text(t.case_no)).append($("<td>").text(t.medical_no)).append($("<td>").text(t.registration_no)).append($("<td>").text(t.section_card_no)).append($("<td>").text(t.temp_no)).append($("<td>").text(t.nationality)).append($("<td>").text(t.languages)).append($("<td>").text(t.skills)).append($("<td>").text(t.remarks))}pagination=e(13);var r;$(function(){$("#filter input").on("change keyup",function(t){var a=t.keyCode;if(0==a||8==a||13==a||27==a||46==a||a>=48&&a<=90||a>=96&&a<=111){var e=$(this);$("#filter-status").html("");var p=$("#results-table tbody");p.empty(),p.append($("<tr>").append($("<td>").text("Searching...").attr("colspan",10))),clearTimeout(r),r=setTimeout(function(){27==a&&e.val("").focus(),13==a&&e.blur(),n(1)},300)}}),$("#reset-filter").on("click",function(){$('#filter input[name="family_name"]').val(""),$('#filter input[name="name"]').val(""),$('#filter input[name="case_no"]').val(""),$('#filter input[name="medical_no"]').val(""),$('#filter input[name="registration_no"]').val(""),$('#filter input[name="section_card_no"]').val(""),$('#filter input[name="temp_no"]').val(""),$('#filter input[name="nationality"]').val(""),$('#filter input[name="languages"]').val(""),$('#filter input[name="skills"]').val(""),$('#filter input[name="remarks"]').val(""),n(1)}),n(1)})},242:function(t,a,e){t.exports=e(145)}});