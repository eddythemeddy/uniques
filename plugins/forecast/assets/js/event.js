(function() {

	let _events = {},
		_doc = $(document),
		_recipesTable = $('#forecast-recipes');

	const _init = () => {
		_events.caculateAmount.init();
		_events.recipes.init();
		_doc.on('change keyup','.unique-total-amount, .unique-total-amount-actual',function(){
			$.when(
				makeSureValidAmt($(this))
			).done(function(){
				_events.caculateAmount.pop($(this), 1);
			});
		});

		_doc.on('change', '#input-recipe', function() {
			_events.recipes.addSubRecipe($(this));
		})

		_doc.on('click', '#close-sale', function() {
			_closeSale();
		})
	}

	const makeSureValidAmt = (th) => {
		let amtval = th.val();
		if(th.hasClass('unique-total-amount-actual')) {
			th.val(amtval < 0 ? 0 : amtval);
		} else {
			th.val(amtval <= 0 ? 1 : amtval);
		}
	};

	_events.recipes = {
		init: function() {
			_doc.on('click','.remove-forecast-recipe', function(){
				//check how many total recipes there are
				let totalExistingRecs = _recipesTable.find('tbody > tr').length,
					forecastRecipeId = $(this).closest('tr').data('forecast-recipe-id');

				$.confirm({
				    title: 'Are you sure?',
				    content: "Delete this recipe",
				    buttons: {
				        cancel: {
				        	btnClass: 'btn-primary',
				        	text: 'Yes',
				        	action: () => {
								_events.recipes.delete(forecastRecipeId);
					        }
				        },
				        confirm: {
				        	btnClass: 'btn-danger',
				        	text: 'No',
				        	action: () => {
				        	}
					    }
				    }
				});
			})
		},
		delete: function(forRecId) {
			$.ajax({
                dataType: "json",
                type: "POST",
                data: { 
                    deleteForecastSubRecipe: 1,
                    forRecId: forRecId
                },
                success: (d) => {
                	$('tr[data-forecast-recipe-id="' + forRecId + '"]').remove();
                },
                error: () => {
                }
            });
		},
		addSubRecipe: function(th) {
			$.ajax({
                dataType: "json",
                type: "POST",
                data: { 
                    addRecipeToEvent: 1,
                    recipe_id: $(th).find(':selected').data('recipe-id'),
                    sub_recipe_id: th.val()
                },
                success: (d) => {
                	if(d.r == 'success') {
						$.when(
	                		_events.recipes.template(
	                			d.event_type,
	                			d.data.sub_recipe_id,
	                			d.data.instantaneous_subrecipe_price,
	                			d.data.forecast_recipe_id,
	                			d.data.sub_recipe_name,
	                			d.data.net_total
	                		)
						).done(function(){
							_events.caculateAmount.net();
						});
                	} else {                		
		                $('body').pgNotification({
		                    style: 'flip',
		                    message: d.message,
		                    position: 'top-right',
		                    timeout: 0,
		                    type: d.r
		                }).show(),
		                setTimeout(function(){
		                    $('.pgn-wrapper').remove();
		                },3000);
                	}
                },
                error: () => {
                }
            }).done(function() {
				th.val('').trigger('change.select2');
			});
		},
		template: function(event_type, sub_recipe_id, instantaneous_subrecipe_price, forecast_recipe_id, sub_recipe_name, net_total) {
			let html = '<tr data-sub-recipe-id="' + sub_recipe_id + '" ' +
							'data-sub-recipe-price="' + instantaneous_subrecipe_price + '" ' +
							'data-forecast-recipe-id="' + forecast_recipe_id + '">' +
							'<td class="v-align-middle p-t-10 p-b-10 p-l-20 p-r-20">' +
								'<a class="remove-forecast-recipe tip" data-toggle="tooltip" data-original-title="Remove this recipe"><i class="fa fa-trash"></i></a>' +
							'</td>' +
							'<td class="b-r b-dashed b-grey p-t-10 p-b-10">' + 
								'<span class="tip" data-toggle="tooltip" data-original-title="' + sub_recipe_name + '">' + sub_recipe_name + '</span>' + 
							'</td>' +
							'<td class="v-align-middle text-center text-primary p-t-10 p-b-10" style="width: 20%;">'+
								'$' + _commas(instantaneous_subrecipe_price.toFixed(2)) +
							'</td>' +
							'<td class="v-align-middle text-center text-center p-t-10 p-b-10" style="width: 20%;">' +
								'<div style="margin: 0 auto;width: auto;display: inline-block;">' +
									'<button class="pull-left amount minus">-</button>' +
									'<input type="number" value="1" class="text-center pull-left unique-total-amount">' +
									'<button class="pull-left amount plus">+</button>' +
								'</div>' +
							'</td>' +
							'<td class="v-align-middle p-t-10 p-b-10 text-center">' +
								'<span class="bold fs-15 net-price">' +
									'$' + _commas(net_total.toFixed(2)) +
								'</span>' +
							'</td>' +
						'</tr>';
			$('#forecast-recipes').prepend(html);
		}
	}

	_events.caculateAmount = {
		init: function() {
			_doc.on('click','.amount, .amount-actual', function() {
				_events.caculateAmount.pop($(this));
			})
		},
		pop: function(th, keyup = false){
			$.when(
				_events.caculateAmount.run(th,keyup)
			).done(function(){
				_events.caculateAmount.net();
			});
		},
		run: function(th, keyup = false){ 
			let tr = th.closest('tr'),
				actual = th.hasClass('amount-actual') || th.hasClass('unique-total-amount-actual') ? true : null,
				subRecipeId = tr.data('sub-recipe-id'),
				subPrice = tr.data('sub-recipe-price'),
				forRecId = tr.data('forecast-recipe-id'),
				amt = th.hasClass('amount-actual') || th.hasClass('unique-total-amount-actual') ? tr.find('.unique-total-amount-actual') : tr.find('.unique-total-amount'),
				amtval = parseInt(amt.val()),
				newAmt = null,
				netPrice = th.hasClass('amount-actual') || th.hasClass('unique-total-amount-actual') ? tr.find('.net-price-actual') : tr.find('.net-price');

			if(!keyup) {
				if(th.hasClass('minus')) {
					if(actual) {
						newAmt = (amtval - 1) <= 0 ? 0 : (amtval - 1);
					} else {
						newAmt = (amtval - 1) === 0 ? 1 : (amtval - 1);
					}
				} else {
					newAmt = amtval + 1;
				}
			} else {
				newAmt = amtval;
			}

			amt.val(newAmt),
			netPrice.html('$' + _commas((newAmt * parseFloat(subPrice)).toFixed(2))),
			_events.caculateAmount.save(forRecId, subRecipeId, newAmt, actual);
		},
		save: function(forRecId, subRecipeId, amt, actual = null) {
			console.log(amt);
			$.ajax({
                dataType: "json",
                type: "POST",
                data: { 
                    udpateForecastSubRecipe: 1,
                    forRecId: forRecId,
                    subRecipeId: subRecipeId,
                    amt: amt,
                    actual: actual
                },
                success: (d) => {
                },
                error: () => {
                }
            });
		},
		net: function(){
			let net = 0;

			$('tr[data-sub-recipe-id]').each(function(){
				let th = $(this),	
					amt = th.find('input.unique-total-amount').val(),
					price = th.data('sub-recipe-price'),
					netTotal = parseFloat(price) * parseFloat(amt);
					
				net = netTotal + net;
			});
			$('#net-total').text(_commas(net.toFixed(2)));
		}
	};

	const _closeSale = function(){
		$.confirm({
		    title: 'Close Sale',
		    content: 'By closing this sale, you have fully chosen how much you have actually sold' +
		    		' and how much you forecasted to sell. <br/><br/>Are you happy with your results so far?',
		    buttons: {
		        cancel: {
		        	btnClass: 'btn-primary',
		        	text: 'Yes Close',
		        	action: () => {
						_events.recipes.delete(forecastRecipeId);
			        }
		        },
		        confirm: {
		        	btnClass: 'btn-danger',
		        	text: 'No',
		        	action: () => {
		        	}
			    }
		    }
		});
	}

	const _commas = function(num) {
		return num.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	}

	return {
		init: _init()
	}
})();