!function(a){function t(n){if(e[n])return e[n].exports;var r=e[n]={i:n,l:!1,exports:{}};return a[n].call(r.exports,r,r.exports,t),r.l=!0,r.exports}var e={};t.m=a,t.c=e,t.i=function(a){return a},t.d=function(a,e,n){t.o(a,e)||Object.defineProperty(a,e,{configurable:!1,enumerable:!0,get:n})},t.n=function(a){var e=a&&a.__esModule?function(){return a.default}:function(){return a};return t.d(e,"a",e),e},t.o=function(a,t){return Object.prototype.hasOwnProperty.call(a,t)},t.p="",t(t.s=42)}({12:function(a,t,e){function n(a){$("#filter-status").html("");var t=$("#results-table tbody");t.empty(),t.append($("<tr>").append($("<td>").text("Searching...").attr("colspan",10)));var e=$("#paginator");e.empty();var i=$("#paginator-info");i.empty(),$.post(filterUrl,{_token:csrfToken,family_name:$('#filter input[name="family_name"]').val(),name:$('#filter input[name="name"]').val(),case_no:$('#filter input[name="case_no"]').val(),medical_no:$('#filter input[name="medical_no"]').val(),registration_no:$('#filter input[name="registration_no"]').val(),section_card_no:$('#filter input[name="section_card_no"]').val(),nationality:$('#filter input[name="nationality"]').val(),languages:$('#filter input[name="languages"]').val(),skills:$('#filter input[name="skills"]').val(),remarks:$('#filter input[name="remarks"]').val(),page:a},function(a){t.empty(),a.data.length>0?($.each(a.data,function(a,e){t.append(r(e))}),pagination.updatePagination(e,a,n),i.html(a.from+" - "+a.to+" of "+a.total)):t.append($("<tr>").addClass("warning").append($("<td>").text("No results").attr("colspan",10)))}).fail(function(a,e){t.empty(),t.append($("<tr>").addClass("danger").append($("<td>").text(e).attr("colspan",10)))})}function r(a){return $("<tr>").attr("id","person-"+a.id).append($("<td>").append($("<a>").attr("href","people/"+a.id).text(a.family_name))).append($("<td>").append($("<a>").attr("href","people/"+a.id).text(a.name))).append($("<td>").text(a.case_no)).append($("<td>").text(a.medical_no)).append($("<td>").text(a.registration_no)).append($("<td>").text(a.section_card_no)).append($("<td>").text(a.nationality)).append($("<td>").text(a.languages)).append($("<td>").text(a.skills)).append($("<td>").text(a.remarks))}pagination=e(2);var i;$(function(){$("#filter input").on("change keyup",function(a){var t=a.keyCode;if(0==t||8==t||13==t||27==t||46==t||t>=48&&t<=90||t>=96&&t<=111){var e=$(this);$("#filter-status").html("");var r=$("#results-table tbody");r.empty(),r.append($("<tr>").append($("<td>").text("Searching...").attr("colspan",10))),clearTimeout(i),i=setTimeout(function(){27==t&&e.val("").focus(),13==t&&e.blur(),n(1)},300)}}),$("#reset-filter").on("click",function(){$('#filter input[name="family_name"]').val(""),$('#filter input[name="name"]').val(""),$('#filter input[name="case_no"]').val(""),$('#filter input[name="medical_no"]').val(""),$('#filter input[name="registration_no"]').val(""),$('#filter input[name="section_card_no"]').val(""),$('#filter input[name="nationality"]').val(""),$('#filter input[name="languages"]').val(""),$('#filter input[name="skills"]').val(""),$('#filter input[name="remarks"]').val(""),n(1)}),n(1)})},2:function(a,t){function e(a,t,e){for(a.empty(),t.current_page>1?a.append(n("&laquo;",1,null,e)):a.append(n("&laquo;",null,"disabled",e)),t.current_page>1?a.append(n("&lsaquo;",t.current_page-1,null,e)):a.append(n("&lsaquo;",null,"disabled",e)),i=2+Math.max(2-(t.last_page-t.current_page),0);i>=1;i--)t.current_page>i&&a.append(n(t.current_page-i,t.current_page-i,null,e));for(a.append(n(t.current_page,null,"active",e)),i=1;i<=2+Math.max(0,3-t.current_page);i++)t.current_page+i-1<t.last_page&&a.append(n(t.current_page+i,t.current_page+i,null,e));t.current_page<t.last_page?a.append(n("&rsaquo;",t.current_page+1,null,e)):a.append(n("&rsaquo;",null,"disabled",e)),t.current_page<t.last_page?a.append(n("&raquo;",t.last_page,null,e)):a.append(n("&raquo;",null,"disabled",e))}function n(a,t,e,n){var r=$("<li>").addClass("page-item");return null!=t?r.append($("<a>").addClass("page-link").attr("href","javascript:;").html(a).on("click",function(){n(t)})):r.append($("<span>").addClass("page-link").html(a)),null!=e&&r.addClass(e),r}a.exports={updatePagination:e}},42:function(a,t,e){a.exports=e(12)}});