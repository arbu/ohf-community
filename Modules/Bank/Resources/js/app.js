import { showSnackbar, handleAjaxError } from '@app/utils'
import scanQR from '@app/qr'

import Vue from 'vue'

import Icon from '@app/components/Icon'
import BankPersonCard from './components/BankPersonCard.vue'

Vue.component('icon', Icon);

new Vue({
	el: '#bank-app',
	components: {
		Icon,
		BankPersonCard
	}
});

$(function(){

	// Scan QR code card and search for the number
	$('#scan-id-button').on('click', () => {
		scanQR((content) => {
			// TODO input validation of code
			$('#bank-container').empty().html('Searching card ...');
			document.location = '/bank/withdrawal/cards/' + content;
		});
	});

	// Coupon
	$('.give-coupon').on('click', handoutCoupon);

	enableFilterSelect();
});

function enableFilterSelect() {
	$('#filter').off('click');
	$('#filter').on('focus', () => {
		$(this).select();
	});
}

function handoutCoupon(){
	var btn = $(this);
	var url = btn.data('url');
	var amount = btn.data('amount');
	var qrCodeEnabled = btn.data('qr-code-enabled');
	if (qrCodeEnabled) {
		scanQR((content) => {
			// TODO input validation of code
			sendHandoutRequest(btn, url, {
				"amount": amount,
				'code': content,
			});
		});
	} else {
		sendHandoutRequest(btn, url, {
			"amount": amount
		});
	}
}

function sendHandoutRequest(btn, url, postData) {
	btn.attr('disabled', 'disabled');
	axios.post(url, postData)
		.then(response => {
			var data = response.data
			btn.append(' (' + data.countdown + ')');
			btn.off('click').on('click', undoHandoutCoupon);
			showSnackbar(data.message, undoLabel, 'warning', (element) => {
				$(element).css('opacity', 0);
				btn.click();
				enableFilterSelect();
			});

			btn.removeClass('btn-primary').addClass('btn-secondary');
			enableFilterSelect();
		})
		.catch(handleAjaxError)
		.then(() => {
			btn.removeAttr('disabled');
		});
}

function undoHandoutCoupon(){
	var btn = $(this);
	var url = btn.data('url');
	btn.attr('disabled', 'disabled');
	axios.delete(url)
		.then(resonse => {
			var data = resonse.data
			btn.html(btn.html().substring(0, btn.html().lastIndexOf(" (")));
			btn.off('click').on('click', handoutCoupon);
			showSnackbar(data.message);

			btn.removeClass('btn-secondary').addClass('btn-primary');
			enableFilterSelect();
		})
		.catch(handleAjaxError)
		.then(() => {
			btn.removeAttr('disabled');
		});
}

// Highlighting of search results
function highlightText(text) {
	$(".mark-text").each(function(idx) {
		var innerHTML = $( this ).html();
		var index = innerHTML.toLowerCase().indexOf(text.toLowerCase());
		if (index >= 0) {
			innerHTML = innerHTML.substring(0,index) + "<mark>" + innerHTML.substring(index,index+text.length) + "</mark>" + innerHTML.substring(index + text.length);
			$( this ).html(innerHTML);
		}
	});
}
if (typeof highlightTerms !== 'undefined') {
	$(function(){
		highlightTerms.forEach(t => highlightText(t))
	})
}