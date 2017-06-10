var initStoreLocator;
var d = new Date();
var geocoder;
var street;
var _map;
var initTheMap = '';
var lastid='';
var calltodefualt=false;
var defualtLatLong;


$(function() {
    var baseURL = FE.baseURL,

        urls = {
            pathToJS : baseURL + 'js/',
            pathToIcons : baseURL + 'images/icons/',
            pathToXML : baseURL + '',

            pins : {
                regular : ssf_custom_marker,
                active : ssf_active_marker,
                skeuomorph : 'youarehere.png'
            }
        },

        map = {
            el : document.getElementById('storeLocatorMap'),
            infobox : {
                el : document.getElementById('storeLocatorInfobox')
            },
            status : {
                $el : $('#storeLocator__mapStatus'),
                $label : $('#storeLocator__mapStatus__inner'),
                $closer : $('#storeLocator__mapStatus__closer'),
                messages : {
                    loadingGoogleMap             : SSF_N1,
                    loadingGoogleMapUtilities    : SSF_N2,
                    startSearch                  : SSF_N3,
                    gettingUserLocation          : SSF_N4,
                    lookingForNearbyStores       : SSF_N5,
                    lookingForStoresNearLocation : SSF_N6,
                    filteringStores              : SSF_N7,
                    cantLocateUser               : SSF_N8,
                    notAllowedUserLocation       : SSF_N9,
                    noStoresNearSearchLocation   : SSF_N10,
                    noStoresNearUser             : SSF_N11,
                    noStoresFromFilter           : SSF_N12,
                    cantGetStoresInfo            : SSF_N13,
                    noStoresFoundNearUser        : SSF_N14,
                    noStoresFound                : SSF_N15,
                    storesFound                  : SSF_N16,
                    generalError                 : SSF_N17
                },
                duration : 5000
            }
        },

        autocompleter = {
            el : document.getElementById('storeLocator__searchBar'),
            placeholderMediumUp : document.getElementById('storeLocator__searchBar').placeholder,
            placeholderSmallDown : 'Search'
        },

        geolocator = {
            $el : $('#geolocator'),
            currentState : 'neutral',
            states : {
                NEUTRAL : 'neutral',
                RUNNING : 'running'
            }
        },

        $els = {
            map : $('#storeLocatorMap'),
            storeList : $('#storeLocator__storeList'),
            topHalf : $('#storeLocator__topHalf'),
            currentStoreCount : $('#storeLocator__currentStoreCount'),
            totalStoreCount : $('#storeLocator__totalStoreCount'),

            storeLocatorInfoBox : {
                self : $('#storeLocatorInfobox'),
                init : function() {
                    this.location = this.self.find('.store-location');
                    this.address = this.self.find('.store-address');
					this.website = this.self.find('.store-website');
					this.embedvideo = this.self.find('.store-embedvideo');
					this.defaultmedia = this.self.find('.store-defaultmedia');
					this.email = this.self.find('.store-email');
					this.contactus = this.self.find('.store-contactus');
					this.telephone = this.self.find('.store-tel');
					this.description = this.self.find('.store-description');
                    this.operatingHours = this.self.find('.store-operating-hours');
                    this.productsServices = this.self.find('.store-products-services');
					this.store_marker = this.self.find('.store_marker');
                    this.directions = this.self.find('.infobox__cta');
					this.streetview = this.self.find('.infobox__stv');
                }
            },
            mobileStoreLocatorInfobox : {
                self : $('#mobileStoreLocatorInfobox'),
                init : function() {
                    this.location = this.self.find('.store-location');
                    this.address = this.self.find('.store-address');
					this.website = this.self.find('.store-website');
					this.embedvideo = this.self.find('.store-embedvideo');
					this.defaultmedia = this.self.find('.store-defaultmedia');
					this.contactus = this.self.find('.store-contactus');
					this.email = this.self.find('.store-email');
					this.telephone = this.self.find('.store-tel');
					this.description = this.self.find('.store-description');
                    this.operatingHours = this.self.find('.store-operating-hours');
                    this.productsServices = this.self.find('.store-products-services');
					this.store_marker = this.self.find('.store_marker');
                    this.directions = this.self.find('.infobox__cta');
					this.streetview = this.self.find('.infobox__stv');
                }
            },
            filters : {
                init : function() {
                    this.states = $('#filter__states').find('input[name="storesState"]');
                    this.outletTypes = $('#filter__outlets').find('input[name="storesOutletType"]');
                    this.productsServices = $('#filter__services').find('input[name="storesProductsServices"]');
                    return this;
                }
            }
        },

        legend,

        xml = {
            filename : 'data.php?t='+d.getTime()
        },

        isLocal = (window.location.hostname === '' || window.location.hostname === 'localhost'),
        isLargeScreen = true,
        isMediumScreen = true;

    setupMapStatus();


    /*---- Filter togglers ----*/


    var $filterTogglers = $('.filter__toggler'),
        $filterTogglerContents = $('.filter__toggler-contents');

    $filterTogglers.togglerify({
        singleActive: true,
        slide: true,
        content: function(index) {
            return $filterTogglerContents.eq(index);
        }
    });
	
	
	var $infoToggler = $('.info__toggler'),
    $infoTogglerContents = $('.info__toggler-contents');

    $infoToggler.togglerify({
        singleActive: true,
        slide: true,
        content: function(index) {
            return $infoTogglerContents.eq(index);
        }
    });

    FE.watchSize('large', function(isLargeScreen) {
        var settings = (isLargeScreen) ? [true, 'toggleOff', 'activate'] : [false, 'toggleOn', 'deactivate'];

        $filterTogglers
            .togglerify('set', 'singleActive', settings[0])
            .togglerify(settings[1], { noSlide : true })
            .togglerify(settings[2]);

        autocompleter.el.placeholder = (isLargeScreen) ? autocompleter.placeholderMediumUp : autocompleter.placeholderSmallDown;
    });


    /*---- Filter popup ----*/


    var $storeLocatorFilterToggler = $('#storeLocatorFilterToggler'),
        $filterPanel = $els.topHalf.find('.filter');

    $filterPanel.data('filter-popup', {
        reveal : function() {
            $('body').addClass('filter-popup-is-shown');
        },

        conceal : function() {
            $('body').removeClass('filter-popup-is-shown');
        }
    });

    $filterPanel.find('[data-close-popup]').on('click', function(e) {
        e.preventDefault();
        $filterPanel.data('filter-popup').conceal();
    });

    $storeLocatorFilterToggler.on('click', function(e) {
        e.preventDefault();
        $filterPanel.data('filter-popup').reveal();
    });


    /*---- Store Locator functions ----*/


    map.status.notify({
        message : 'loadingGoogleMap',
        loadingIndicator : true
    });
	var googleApi=''
	if(ssf_google_api!='' && ssf_google_api!='undefined'){
		googleApi='key='+ssf_google_api+'&'
	}
    $.getScript('https://maps.googleapis.com/maps/api/js?'+googleApi+'sensor=false&libraries=places&v=3.15&language='+ssf_m_lang+'&region='+ssf_m_rgn+'&callback=initStoreLocator');

    initStoreLocator = function() {
        map.status.notify({
            message : 'loadingGoogleMapUtilities',
            loadingIndicator : true
        });

        $.getScript(urls.pathToJS + 'plugins/google-maps-utility-library/marker-with-label.packed.js', onLoad);
        $.getScript(urls.pathToJS + 'plugins/google-maps-utility-library/infobox.packed.js', onLoad);

        var loadCounter = 0;

        function onLoad() {
            loadCounter++;
            if(loadCounter < 2) return;

            map.status.notify({
                message: 'startSearch',
                autoclose: true
            });

            /*---- Setup variables ----*/

            $.extend(true, map, {
                markers : {
                    user : {},
                    stores : {
                        list : []
                    }
                }
            });

            /* Setup infobox variables */
            $els.storeLocatorInfoBox.init();
            $els.mobileStoreLocatorInfobox.init();

            setupMediaQueries();
            setupGeolocator();
            setupAutocompleter();
            setupEventHandlers();


            /*---- Setup XML ----*/

            if(typeof xml.data === 'undefined') {
                $.ajax({
                    type: "GET",
                    url: urls.pathToXML + xml.filename,
                    dataType: "xml",
                    success: function(data) {
                        xml.data = data;
                        continueInit();
                    }
                });
            } else {
                continueInit();
            }

            function continueInit() {
                /*---- Setup legend ----*/

                legend = {};
                var radiosStrArray = [];
                $(xml.data).find('label').each(function() {
                    var tag = $(this).find('tag').text().trim();
                    var copy = $(this).find('copy').text().trim();

                    legend[tag] = copy;
                    radiosStrArray.push([
                        '<label class="label--vertical-align">',
                            '<div class="label__input-icon">',
                                '<i class="icon icon--input icon--checkbox-btn"></i>',
                            '</div>',
                            '<div class="label__contents">',
                                '<input type="checkbox" name="storesProductsServices" value="', tag, '" /> ', copy,
                            '</div>',
                        '</label>'
                    ].join(''));
                });

                $('#productsServicesFilterOptions').append(radiosStrArray.join(''));

                $els.filters.init().productsServices.inputify();

                /*---- Setup store count ----*/

                $els.totalStoreCount.text($(xml.data).find('item').length);

                startMap();
            }
        }

        function startMap() {
		
		var defaultLatLng;
		if(default_location!='' && default_location!==undefined && geo_settings =='1'){
			  geocoder = new google.maps.Geocoder();
			  geocoder.geocode( {'address':default_location,'region':''}, function(results, status) {
						if(status == google.maps.GeocoderStatus.OK) {
							defaultLatLng = results[0].geometry.location;
							loadDefaultMap(defaultLatLng);
							}
							
					});
			}else{
			   defaultLatLng = new google.maps.LatLng(40.705, -74.0139);
			   loadDefaultMap(defaultLatLng);
			}			
		
		
        }
		
		
		
		function loadDefaultMap(defaultLatLng){
		defualtLatLong=defaultLatLng;
		mapWithStyle = {
					center: defaultLatLng,
					styles:ssf_map_code,
					streetViewControl: true,
					streetViewControlOptions: {
						position: google.maps.ControlPosition.RIGHT_TOP
					},
					zoom: 7,
					zoomControl: true,
					zoomControlOptions: {
						position: google.maps.ControlPosition.RIGHT_TOP
					},
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					scrollwheel: false
				   };
				   
				   mapWithoutStyle = {
					center: defaultLatLng,
					streetViewControl: true,
					streetViewControlOptions: {
						position: google.maps.ControlPosition.RIGHT_TOP
					},
					zoom: 7,
					zoomControl: true,
					zoomControlOptions: {
						position: google.maps.ControlPosition.RIGHT_TOP
					},
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					scrollwheel: false
				   };
				   
				   
		if(ssf_map_code!="" && ssf_map_code!=undefined)
				{
					map.self = new google.maps.Map(map.el, mapWithStyle);

				} else {
						map.self = new google.maps.Map(map.el, mapWithoutStyle);
				}
			
            map.infobox.self = new InfoBox({
                content: map.infobox.el,
                pixelOffset: new google.maps.Size(35, -240),
                closeBoxMargin: "10px 10px 0 0",
                closeBoxURL: "images/icons/cross-white.png"
            });

            $els.storeLocatorInfoBox.self.find('.infobox__closer').on('click', function(e) {
                e.preventDefault();
                map.infobox.self.close();
            });

            geolocator.watch();
		
		
		}
		
		
		
		 var multicategoryVal =function(){
				var categories;
				if ($('input#storesProductsServices').is(':checked')) {
					categories=$('#storesProductsServices').val();
				}
				else{
							
								var $langSelect = $("input[type=checkbox][name='storesProductsServices']:checked");
								if ($langSelect.length) {
								 categories = $langSelect.map(function(){
									return this.value;
							   }).get();
							} else {
								categories='';
							}
				}
				return categories;
			}

        function setupGeolocator() {
            $.extend(true, geolocator, {
                watch : function() {
                    var self = this;

                    self.currentState = self.states.RUNNING;

                    map.status.notify({
                        message: 'gettingUserLocation',
                        loadingIndicator: true
                    });
                
                    self.$el.addClass('is-loading');
					
					
            if(initTheMap=='' && geo_settings=='2'){
					$('#filterShowAll').trigger('click');

			} else if(initTheMap=='' && default_location!='' && geo_settings=='0'){
						$('input#storeLocator__searchBar').val(default_location);
						$('.icon--search').trigger('click');
			} else {
                    map.watcher = navigator.geolocation.watchPosition(
                        /* Geolocation is successful */
                        function(position) {
                            if(self.currentState !== self.states.RUNNING) return;
                            self.rest();
							calltodefualt==true;
                            
                            var coordinates = position.coords,
                                userLatLng = new google.maps.LatLng(coordinates.latitude, coordinates.longitude);
                               
                            setMainMapMarker(userLatLng, 'Your current location');

                            map.status.notify({
                                message : 'lookingForNearbyStores',
                                loadingIndicator : true
                            });
							/* Category filter*/
							var categories=multicategoryVal();
                            searchForStores({
                                latLng : userLatLng,
                                distance : ssf_mega_distance,
								productsServices : categories,
                                centerOnUser : true,
                                onError : function() {
                                    map.status.notify({
                                        message : 'noStoresNearUser',
                                        closeable : true
                                    });
                                }
                            });
                        },
                        /* Geolocation has failed */
                        function(error) {
                            if(self.currentState !== self.states.RUNNING) return;
                            self.rest();
                            
                            switch(error.code) {
                                case error.TIMEOUT:
                                    map.status.notify({
                                        message : 'cantLocateUser',
                                        closeable : true
                                    });
                                break;
                                
                                default:
                                    locationNotAvailable();
                                break;
                            }
                        },
                        /* Geolocation settings */
                        {
                            enableHighAccuracy: true,
                            maximumAge: 30000,
                            timeout: 15000 /* 15 seconds */
                        }
                    );
				}
				
				initTheMap='1';
				
                    setTimeout(function() {
                        navigator.geolocation.clearWatch(map.watcher);
                    }, 20000)
                    
                    /**
                     * For cases like Firefox where if user chooses to temporarily deny
                     * permission to their location (by choosing "Not Now"), the geolocation
                     * function will NOT trigger any callback.
                     *
                     * This timeout is to make sure that something WILL happen after a
                     * specific amount of time, and we will be assuming that the geolocation
                     * has failed.
                     **/
                    geolocator.timer = setTimeout(function() {
                        /* hideMapFeedback(); */
						if(geo_settings=='1' && calltodefualt==false){ defaultLocationStore(); }
                        self.rest();
                        locationNotAvailable();
                    }, 20000);
                },

                rest : function() {
                    if(!navigator.geolocation) return;

                    geolocator.currentState = geolocator.states.NEUTRAL;
                    navigator.geolocation.clearWatch(map.watcher);
                    clearTimeout(geolocator.timer);
                    geolocator.$el.removeClass('is-loading');
                }
            });
        }
		
		
		function defaultLocationStore(){
					searchForStores({
                                latLng : defualtLatLong,
                                distance : ssf_mega_distance,
                                centerOnUser : true,
                                onError : function() {
                                    map.status.notify({
                                        message : 'noStoresNearUser',
                                        closeable : true
                                    });
                                }
                            });	
		}

        function setupMediaQueries() {
            FE.watchSize('large', function(mq) {
                isLargeScreen = mq;

                if(typeof map.markers.stores.current === 'undefined'
                || map.markers.stores.current === null) {
                    return;
                }

                if(isLargeScreen) {
                    map.infobox.self.open(map.self, map.markers.stores.current);
                }
            });

            FE.watchSize('medium', function(mq) {
                isMediumScreen = mq;

                if(typeof map.markers.stores.current === 'undefined'
                || map.markers.stores.current === null) {
                    return;
                }

                if(isMediumScreen) {
                    map.infobox.self.open(map.self, map.markers.stores.current);
                } else {
                    setMapCenter(map.markers.stores.current.position);
                    map.infobox.self.close();
                }
            });
        }

        function setupAutocompleter() {
		
			 if(ssf_defualt_region=='false'){
               var _autocompleter = new google.maps.places.Autocomplete(autocompleter.el, {
                    componentRestrictions: {'country': ssf_m_rgn}
                });
             }else{
				var _autocompleter = new google.maps.places.Autocomplete(autocompleter.el, {
                });
            }

            google.maps.event.addListener(_autocompleter, 'place_changed', function() {
			
				 $('div[title="Exit Street View"]').trigger('click');
                 var searchPlace = $('#storeLocator__searchBar').val();

				  if(searchPlace!=''){
				  geocoder = new google.maps.Geocoder();
				  
				  geocoder.geocode( {'address':searchPlace,'region':ssf_m_rgn}, function(results, status) {
						
							if(status == google.maps.GeocoderStatus.OK) {
								placeLocation = results[0].geometry.location;
								
								 setMainMapMarker(placeLocation, 'Your search location: ' + searchPlace);
								var categories=multicategoryVal();
								searchForStores({
									latLng : placeLocation,
									distance : ssf_mega_distance,
									productsServices : categories,
									centerOnUser : true,
									onError : function() {
										map.status.notify({
											message : 'noStoresNearSearchLocation',
											closeable : true
										});
									}
								});
							
							} else {
								
								
							}
						});
				  

							   
					}
            });
			
			$( ".icon--search" ).click(function() {

			  $('div[title="Exit Street View"]').trigger('click');
			  var searchPlace = $('#storeLocator__searchBar').val();

			  if(searchPlace!=''){
			  geocoder = new google.maps.Geocoder();
			  
			  geocoder.geocode( {'address':searchPlace,'region':ssf_m_rgn}, function(results, status) {
					
						if(status == google.maps.GeocoderStatus.OK) {
							placeLocation = results[0].geometry.location;
							
							 setMainMapMarker(placeLocation, 'Your search location: ' + searchPlace);
							//categories fixed 
						    var categories=multicategoryVal();
						
							searchForStores({
								latLng : placeLocation,
								distance : ssf_mega_distance,
								productsServices : categories,
								centerOnUser : true,
								onError : function() {
									map.status.notify({
										message : 'noStoresNearSearchLocation',
										closeable : true
									});
								}
							});
						
						} else {
							
							
						}
					});
			  

						   
				}
			});
			
			
			$( "input[name='storesRegion']" ).click(function() {
			

			$('div[title="Exit Street View"]').trigger('click');
			  var searchPlace = $("input[name=storesRegion]:checked").val()
			$('input#storeLocator__searchBar').val(searchPlace);
			  if(searchPlace!=''){
			  geocoder = new google.maps.Geocoder();
			  
			  geocoder.geocode( {'address':searchPlace,'region':ssf_m_rgn}, function(results, status) {
					
						if(status == google.maps.GeocoderStatus.OK) {
							placeLocation = results[0].geometry.location;
							
							 setMainMapMarker(placeLocation, 'Your search location: ' + searchPlace);
							var categories=multicategoryVal();
							searchForStores({
								latLng : placeLocation,
								distance : ssf_mega_distance,
								productsServices : categories,
								centerOnUser : true,
								onError : function() {
									map.status.notify({
										message : 'noStoresNearSearchLocation',
										closeable : true
									});
								}
							});
						
						} else {
							
							
						}
					});
			  

						   
				}
			});

            /*
             * FastClick and Google Map's Autocomplete have a conflict with each
             * other: FastClick prevents touchscreen from being able to select
             * the Autocomplete options. To fix this, whenever Autocomplete
             * inserts its results into the DOM, we need to add the `needsclick`
             * class to it.
             *
             * http://stackoverflow.com/questions/9972080/cant-tap-on-item-in-google-autocomplete-list-on-mobile
             */
            $(document).on({
                'DOMNodeInserted': function() {
                    $('.pac-item, .pac-item span', this).addClass('needsclick');
                }
            }, '.pac-container');
        }

        function setupEventHandlers() {
            /* Geolocator */
            geolocator.$el.on('click', function(e) {
                e.preventDefault();
				$('div[title="Exit Street View"]').trigger('click');
                if(!$(this).hasClass('is-loading')) {
                    geolocator.watch();
                }
            });

			
			
            /* Filter options clearer */
            $('#filterOptionsClearer').on('click', function(e) {
                e.preventDefault();
				$('.icon--radio-btn').removeClass('is-checked');
                $.each($els.filters, function(key, value) {
                    if(typeof value !== 'object' || !value instanceof jQuery) return;
                    value.prop('checked', false).inputify('refresh');
                });
            });

            $('#filterShowAll').on('click', function(e) {
                e.preventDefault();
				$('#storesProductsServices').trigger('click');
                searchForStores();
            });
			
			$('#filterShowAllMobile').on('click', function(e) {
                e.preventDefault();
                searchForStores();
            });	
			
			/* Filter category */
			 $('#storesProductsServices').change(function(){
					if(this.checked)
					 	$(".icon--checkbox").addClass("is-checked");
					 else
					 	$(".icon--checkbox").removeClass("is-checked"); 
			 });

            /* Filter applier */
            $('#applyFilterOptions').on('click', function(e) {
			    var categories=multicategoryVal();
                var filterProps = {
                        latLng : (map.markers.user.self)
                            ? map.markers.user.self.position
                            : undefined,
                        state : $els.filters.states.filter(':checked').val(),
                        outletType : $els.filters.outletTypes.filter(':checked').val(),
                        productsServices : categories,
                        onError : function() {
                            map.status.notify({
                                message : 'noStoresFromFilter',
                                closeable : true
                            });
                        }
                    };

                if(typeof filterProps.state === 'undefined') {
                    $.extend(filterProps, {
                        distance : ssf_mega_distance,
                        centerOnUser : true
                    });
                }

                searchForStores(filterProps);
            });

            /* Mobile infobox closer */
            $els.mobileStoreLocatorInfobox.self.find('.infobox__closer').on('click', function(e) {
                e.preventDefault();
                $els.mobileStoreLocatorInfobox.self.removeClass('is-shown');
            });

            /* Store List */
            $els.storeList.on('click', '.store-locator__infobox', function(e) {
                e.preventDefault();
                setCurrentStoreDetails($(this));
				
		
		
		
		
            });

            /* Map Status Response */
            map.status.$closer.on('click', function(e) {
                e.preventDefault();
                map.status.conceal();
            });

            /* Get Directions */
            $els.storeList.on('click', 'a', function(e) {
                e.stopPropagation();
            });
        }

        function makeStoreDetailsString($storeXMLElem, index, useLabel) {
		
		
            var _store = {
                    lat : getText($storeXMLElem.find('latitude')),
                    lng : getText($storeXMLElem.find('longitude')),
					storeimage : getText($storeXMLElem.find('storeimage')),
                    location : getText($storeXMLElem.find('location')),
                    address : getText($storeXMLElem.find('address')),
					website : getText($storeXMLElem.find('website')),
					embedvideo : getText($storeXMLElem.find('embedvideo')),
					defaultmedia : getText($storeXMLElem.find('defaultmedia')),
					email : getText($storeXMLElem.find('email')),
					telephone : getText($storeXMLElem.find('telephone')),
					description : getText($storeXMLElem.find('description')),
                    operatingHours : getText($storeXMLElem.find('operatingHours')),
					store_marker : getText($storeXMLElem.find('store_marker'))
                },
                letter = '',
                clearClass = '',
                getDirections = '<div class="infobox__row infobox__cta">&nbsp;</div>';
				getStreetView = '<div class="infobox__row infobox__stv">&nbsp;</div>';
				
            if(_store.lat && _store.lng) {
                letter = translateIntoLetter(index);

                getDirections = [
                    '<a href="https://maps.google.com/maps?',
                            (map.markers.user.self)
                                ? 'saddr=' + map.markers.user.self.getPosition() + '&'
                                : '',
                            'daddr=(', _store.lat, ', ', _store.lng, ')&output=embed"',
                        ' target="_blank"',
                        ' class="infobox__row infobox__cta">',
                        SSF_GET_DIRECTION,
                    '</a>'
                ].join('');
				
				getStreetView = [
                    '<a href="javascript:streetView(',_store.lat, ', ', _store.lng, ')"',
                        ' class="infobox__row infobox__stv">',
                        'Street View',
                    '</a>'
                ].join('');
				
            }

            if(index !== 0 && index % 3 === 0) {
                clearClass += ' medium-clear-left';
            }
        var ssf_image_video='';
		var ssf_image_image='';
	
		if(ssf_show_image_list=='showboth'  && _store.defaultmedia=='video' && _store.embedvideo!='')
		{
			ssf_image_video=base64.decode(_store.embedvideo);
			
		}
			else if(ssf_show_image_list=='showboth' && _store.defaultmedia!='video')
		{
				ssf_image_image=(_store.storeimage!='')? _store.storeimage:'./images/NoImage.png';
		}
		
		if(ssf_tel_fax_link=='true'){
			if(_store.telephone!=''){
			 _store.telephone='<a href="tel:'+ _store.telephone+'">'+ _store.telephone+'</a>';
			 }
			 if(_store.fax!=''){
			 _store.fax='<a href="tel:'+ _store.fax+'">'+ _store.fax+'</a>';
			 }
	    }
            return [
                '<div class="medium-4', clearClass, ' column">',
                    '<div class="store-locator__infobox" id="store', index, '">',
                        '<div class="infobox__row infobox__row--marker">',
                            '<div class="infobox__marker', (letter.length > 1) ? ' infobox__marker--small' : '', '">',
                                letter,
                            '</div>',
                        '</div>',

                        '<div class="infobox__body">',
						'<div class="infobox__row infobox__title   ssf_image_setting" style="background-image: url(',ssf_image_image,');">',
                                ssf_image_video,
                            '</div>',
							'<div class="infobox__row infobox__title   store-image"  style="display:none;">',

                                _store.storeimage,

                            '</div>',
                            '<div class="infobox__row infobox__title   store-location">',
                                _store.location,
                            '</div>',
                            
                            '<div class="infobox__row   store-address">',
                                _store.address, 
                            '</div>',
                             '<div class="infobox__row   store-website" style="display:none;"><a target="new" ',((_store.website.substring(0, 4)!="http") ? 'href="http://'+_store.website : 'href="'+_store.website),'">',
                                _store.website, 
                            '</a></div>',
                            '<div class="infobox__row   store-email" style="display:none;"><a href="mailto:',_store.email,'">',
                                _store.email, 
                            '</a></div>',
							 '<div class="infobox__row   store-contactus" style="display:none;">',
                                _store.email, 
                            '</div>',
							'<div class="infobox__row   store-operating-hours" style="display:none;">',
                                _store.operatingHours, 
                            '</div>',
							'<div class="infobox__row   store-tel" style="display:none;">',
                                _store.telephone, 
                            '</div>',
							'<div class="infobox__row   store-description" style="display:none;">',
                                _store.description, 
                            '</div>',
							'<div class="infobox__row   store-embedvideo" style="display:none;">',
                                _store.embedvideo, 
                            '</div>',
							'<div class="infobox__row   store-defaultmedia" style="display:none;">',
                                _store.defaultmedia, 
                            '</div>',
							'<div class="infobox__row   store_marker" style="display:none;">',
                                _store.store_marker, 
                            '</div>',
                        '</div>',

                        getDirections,
						'<div style="display:none;">',
						getStreetView,
						 '</div>',
                    '</div>',
                '</div>'
            ].join('');
        }
    
        function makeStoreProductsServicesString($storeXMLElem) {
            var servicesStrArr = [];

            $.each(legend, function(tag, copy) {
                var $service = $storeXMLElem.find(tag);
                if(!$service.length) return;

                var serviceBoolStr = $service.text().trim();
                if(serviceBoolStr !== 'true') return;

                servicesStrArr.push([
                    '<li>', copy, '</li>'
                ].join(''));
            });

            return servicesStrArr.join('');
        }

        /**
         * Possible properties for the `settings` Object Literal to be passed
         * into searchForStores():
         
            settings = {
                latLng : (Object),
                state : (String),
                outletTypes : (String),
                productsServices : (String),
                centerOnUser : (Boolean),
                distance : (Number)
            }

         * All properties can be included/excluded as needed.
         **/
        function searchForStores(settings) {
            settings = settings || {};
            geolocator.rest();
            map.infobox.self.close();
            $els.topHalf.addClass('has-searched');
            
            /* Only run the AJAX if there's no cached data */
            if(typeof xml.data === 'undefined') {
                $.ajax({
                    type: "GET",
                    url: xml.url,
                    dataType: "xml",
                    success: function(data) {
                        xml.data = data;
                        addStores();
                    },
                    error : function() {
                        hideMapFeedback();
                        showStatusFeedback(messages.cantGetStoresInfo);
                    }
                });
            } else {
                addStores();
            }
            
            function addStores() {
                var targetItems,
                    storesXMLArray = [];
                    
                /**
                 * We're about to add in the new search results, so have to reset
                 * a few things to get a (relatively) blank slate.
                 **/
                map.markers.stores.current = null;
                $('#page_navigation').html('');
                while(map.markers.stores.list.length) {
                    map.markers.stores.list.shift().setMap(null);
                }

                $els.mobileStoreLocatorInfobox.self.removeClass('is-shown'); //hideCurrentStore();
                
                /* If a state is provided, then narrow down the stores to that state. */
                if(typeof settings.state === 'string' && settings.state !== 'default') {
                    targetItems = $(xml.data).find(settings.state).find('item');
                } else {
                    targetItems = $(xml.data).find('item');
                }

                targetItems.each(function(indexs) {
                    var $thisStore = $(this),
                        storeLatNode = $thisStore.find('latitude'),
                        storeLngNode = $thisStore.find('longitude'),
                        storeLatLng = 'nope',
                        storeDistance = 'nope';
                    
                    /* Only proceed if complete coordinates are available */
                    if(storeLatNode.length && storeLngNode.length) {
                        /**
                         * Calculate distance. Needed to sort from nearest to furthest
                         * later in the function.
                         **/
                        storeLatLng = new google.maps.LatLng(parseFloat(storeLatNode.text()), parseFloat(storeLngNode.text()));
                        storeDistance = (typeof settings.latLng === 'undefined') ? 0 : distHaversine(settings.latLng, storeLatLng);
                        
                        /**
                         * If a set distance is provided, check and make sure it's not
                         * further than that. Otherwise, exit this iteration.
                         **/
                        if(typeof settings.distance === 'number'
                        && storeDistance > settings.distance) {
                            return;
                        }
                    } else if(!settings.state
                           || !$thisStore.parent().is($.trim(settings.state))) {
                        return;
                    }
                    
                    /**
                     * If an outlet type is provided, check and make sure it
                     * matches. Otherwise, exit this iteration.
                     **/
                    if(typeof settings.outletType === 'string'
                    && settings.outletType !== 'default'
                    && $thisStore.find('shopType').text() !== settings.outletType) {
                        return;
                    }
                    
                    /**
                     * If a type of product/service is provided, check and
                     * make sure it matches. Otherwise, exit this iteration.
                     **/
                     if(settings.productsServices !==undefined && settings.productsServices !== 'default' && settings.productsServices !== '') {
						var found=1;
						if(settings.productsServices.constructor === Array)
						{
						for (var x in settings.productsServices){	
								if($thisStore.find(settings.productsServices[x]).text() == 'true')
								 {
									found=0;
								 }
							 }
							 if(found!=0)
							{
							   return; 
							}
						}
						else if($thisStore.find(settings.productsServices).text() !== 'true'){
							return; 
						}
                    }
                    
                    /**
                     * If we've made it this far, means it's okay to add this
                     * store into the arrays to be processed later.
                     **/
                    
                    if(storeLatLng !== 'nope') {
                        map.markers.stores.list.push({
                            latLng : storeLatLng,
                            distance : storeDistance,
                            storeLocation : $thisStore.find('location').text().trim(),
							storeMark : $thisStore.find('store_marker').text().trim()
                        })
                    }
                    
                    storesXMLArray.push({
                        '$xml' : $thisStore,
                        distance : storeDistance
                    });
                });
                
                if(!storesXMLArray.length) {
                    var errorMessage = map.status.messages[settings.errorMessage] || settings.errorMessage;

                    if(typeof settings.onError === 'function') settings.onError('noResults');
                    $els.storeList.html('<div class="text-large text-center">' + map.status.messages.noStoresFound + '</div>');
                    $els.currentStoreCount.text('0');

                    return;
                }

                /* Update store count */
                $els.currentStoreCount.text(storesXMLArray.length);
                
                /**
                 * Sort array contents to be from closet to furthest
                 * in distance.
                 **/
                storesXMLArray.sort(distanceAscendingSorter);
                map.markers.stores.list.sort(distanceAscendingSorter);
                
                var maximumNumberOfLabels = 25,
                    storesStrArr = [];

                for(var i = 0, ii = storesXMLArray.length; i < ii; i++) {
                    storesStrArr.push(makeStoreDetailsString(storesXMLArray[i].$xml, i));
                }
                
                /* Populate the list of stores */
                $els.storeList.html(storesStrArr.join(''));
                $els.storeList.heightSyncify({
                    items : ['.infobox__body']
                });
                map.status.notify({
                    message : 'storesFound',
                    autoclose: true
                });
                
                /* Reset marker bounds */
                map.markers.bounds = new google.maps.LatLngBounds();
                
                var markerLabelClass,
                    markerLabelContent;

                for(var j = 0, jj = map.markers.stores.list.length; j < jj; j++) {
                    /**
                     * Only use labelled pins if there are less than 53 markers
                     * to place. We're using 53 as the reference value, because
                     * we have 26 letters in the alphabet, and we differentiate
                     * between upper case and lower case, thus our total options
                     * are 26 * 2 = 52 letters. So the number of labelled
                     * markers cannot be more than 52.
                     **/
                   // markerLabelContent = translateIntoLetter(j);
					
					if(labeled_marker=='1'){

                    markerLabelContent = translateIntoLetter(j);

					} else {

					markerLabelContent = '';

					}
					
					
					
                    markerLabelClass = 'store-locator__map-pin';

                    if(j > maximumNumberOfLabels) {
                        markerLabelClass += '  store-locator__map-pin--small';
                    } else if(j > maximumNumberOfLabels + 9) {
                        markerLabelClass += '  store-locator__map-pin--xsmall';
                    }
                
                    map.markers.bounds.extend(map.markers.stores.list[j].latLng);
                    var stmarker='';
                    if(map.markers.stores.list[j].storeMark!='')
					{
						stmarker=map.markers.stores.list[j].storeMark;
					}
					else{
						stmarker=urls.pathToIcons + urls.pins.regular;
					}
                    
                    map.markers.stores.list[j] = new MarkerWithLabel({
                        position: map.markers.stores.list[j].latLng,
                        map: map.self,
                        title: map.markers.stores.list[j].storeLocation,
                        icon: stmarker,
                        zIndex: jj-j,
                        labelContent: markerLabelContent,
                        labelClass: markerLabelClass,
                        originalLabelClass: markerLabelClass,
                        labelInBackground: false
                    });
                    
                    /**
                     * Later when we adjust the icon to be in its active state,
                     * the z-ndex will be adjusted so it's always on the top.
                     * This property will then be referenced to reset the
                     * z-index back to its original value.
                     **/
                    map.markers.stores.list[j].originalZIndex = jj-j;
                
                    (function() {
                        var $targetStore = $('#store' + j);
                
                        $targetStore.data('storeMarker', map.markers.stores.list[j]);
                        
                        google.maps.event.addListener(map.markers.stores.list[j], 'click', function() {
                            setCurrentStoreDetails($targetStore);
                        });
                    })();
                }
                
                if(storesXMLArray.length === 1) {
                    setCurrentStoreDetails($('#store0'));
                }
                
                if(settings.centerOnUser) {
                    map.self.fitBounds(map.markers.bounds);
                    setMapCenter(map.markers.user.self.position, true);
                } else {
                    map.self.setCenter(map.markers.bounds.getCenter());
                    map.self.fitBounds(map.markers.bounds);
                    map.self.setZoom(map.self.getZoom()-1);
                }
				ssf_ifrane_vedio();
				if(ssf_pagination>0 && storesXMLArray.length>ssf_pagination){  pagging(ssf_pagination);  }
                if(typeof settings.onSuccess === 'function') settings.onSuccess();
            }
        }
		
		var imgToggleS = function(v,v2){
				$('div .info-img').css('height','150px');
				$('div .info-img').css('background-image','url('+v+')');
				$('#storeLocatorInfobox').children('div .info-img').click(function() {
				  showPopup(v,v2);
				});
							
		};
		
		var imgToggleH = function(){
		  $('div .info-img').css('background-image','url("")');
				$('#storeLocatorInfobox').children('div .info-img').unbind();
				$('div .info-img').css('height','0px');
			
		};

        function setCurrentStoreDetails($targetStoreElem) {
		
		if($targetStoreElem.find('.store-tel').html()==""){
		
		$('#info-tel').css('display','none');
		} else {
		
		$('#info-tel').css('display','block');
		}
		
		
		if($targetStoreElem.find('.store-description').html()==""){

		$('#info-description').css('display','none');
		} else {

		$('#info-description').css('display','block');
		}
		
		if($targetStoreElem.find('.store-website').html()=="" || $targetStoreElem.find('.store-website').html()=='<a target="new" href="http://"></a>'){
		$('#info-website').css('display','none');
		} else {
		$('#info-website').css('display','block');
		}
		
		if($targetStoreElem.find('.store-email').html()=="" || $targetStoreElem.find('.store-email').html()=='<a href="mailto:"></a>'){
		$('#info-email').css('display','none');
		} else {
		$('#info-email').css('display','block');
		}
		
		if($targetStoreElem.find('.store-operating-hours').html()==""){
		 $('#info-operating-hours').css('display','none');
		} else {
		 $('#info-operating-hours').css('display','block');
		}
		
		if($targetStoreElem.find('.store-contactus').html()==""){

		jQuery('.store-contact-us').css('display','none');

		} else {

		jQuery('.store-contact-us').css('display','block');

		}
		$('.info__toggler').addClass('actives');
		$('.info__toggler').removeClass('is-toggled');
		$('.info__toggler-contents').removeClass('is-toggled');
		if(scroll_setting=='true'){
		 $("body").animate({scrollTop:ssf_scroll_top}, 'slow');
		}
		
		$('div[title="Exit Street View"]').trigger('click');
            var _store = {
					storeimage : $targetStoreElem.find('.store-image').html(),
                    location : $targetStoreElem.find('.store-location').html(),
                    address : $targetStoreElem.find('.store-address').html(),
					website : $targetStoreElem.find('.store-website').html(),
					embedvideo : $targetStoreElem.find('.store-embedvideo').html(),
					defaultmedia : $targetStoreElem.find('.store-defaultmedia').html(),
					email : $targetStoreElem.find('.store-email').html(),
					contactus : $targetStoreElem.find('.store-contactus').html(),
					telephone : $targetStoreElem.find('.store-tel').html(),
					description : $targetStoreElem.find('.store-description').html(),
                    operatingHours : $targetStoreElem.find('.store-operating-hours').html(),
                    productsServices : $targetStoreElem.find('.store-products-services').html(),
                    directions : $targetStoreElem.find('.infobox__cta').attr('href'),
					streetview : $targetStoreElem.find('.infobox__stv').attr('href'),
					store_marker: $targetStoreElem.find('.store_marker').html()
                };
            
			if(_store.defaultmedia=='image' || _store.defaultmedia==''){
				
				if(_store.storeimage!=''){
					
				   var splitstr  = _store.storeimage.split("/");
				   var ori_img = 'ori_'+splitstr[splitstr.length-1];
				   var imgpath = '';
				   for(i=0;i<splitstr.length-1;i++){
				   imgpath += splitstr[i]+"/";
				   }
					
					
					//$('div .info-image').html('<a href=javascript:showPopup(\"'+_store.location+'\",\"'+imgpath+ori_img+'\")><img src="'+_store.storeimage+'"></a>');

					setTimeout(function(){	
					$('div .info-img').css('height','150px');
					$('div .info-img').html('');
					$('#storeLocatorInfobox').children('div .info-img').click(function() {
					  showPopup(_store.location,imgpath+ori_img);
					});
					$('.info-img').css('background-image','url('+_store.storeimage+')'); 
					},200);
					//$('div .info-img').html(youtube);
				
					//imgToggleS(_store.location,imgpath+ori_img);
				} else {
					$('div .info-img').html('');
					$('div .info-img').css('background-image','url("")');
					$('#storeLocatorInfobox').children('div .info-img').unbind();
					$('div .info-img').css('height','0px');

					setTimeout(imgToggleH, 200);
				}
				
				} else {
				
				if(_store.embedvideo!=''){
					
					var video = base64.decode(_store.embedvideo);
					//'<iframe width="100%" height="100%" src="https://www.youtube.com/embed/CieuGZ7TthE" frameborder="0" allowfullscreen></iframe>';
					//console.log(atob(_store.embedvideo));
					
					
					setTimeout(function(){	

					$('div .info-img').css('background-image','url("")');
					$('div .info-img').css('height','250px');
					$('div .info-img').html(video);
					var $allVideos = $("iframe[src^='http']");

					$allVideos.each(function() {

					$(this)
						.data('aspectRatio', this.height / this.width)
						
						// and remove the hard coded width/height
						.removeAttr('height')
						.removeAttr('width');

					});
				
					var newWidth = "100%";
				
					$allVideos.each(function() {

						var $el = $(this);
						$el
							.width(newWidth)
							.height(newWidth);

					});
					
					},200);
					
				} else {
					$('div .info-img').html('');
					$('div .info-img').css('background-image','url("")');
					$('#storeLocatorInfobox').children('div .info-img').unbind();
					$('div .info-img').css('height','0px');

					setTimeout(imgToggleH, 200);
				}
				
				}
				if(_store.contactus!='')
				{
					jQuery('#modernBrowserConatct').on('click', '#contact-submit', function() {
						SendMail(_store.contactus);
					})
				}
			
            /**
             * If there is already a Current Store displayed, revert its pin
             * back to the inactive state.
             **/
             if(typeof map.markers.stores.current !== 'undefined' 
            && map.markers.stores.current !== null) {
                map.markers.stores.current.set('labelClass', map.markers.stores.current.originalLabelClass);
				if(lastid!='') { map.markers.stores.current.setIcon(lastid); }
				else {  map.markers.stores.current.setIcon(urls.pathToIcons + urls.pins.regular); }
                map.markers.stores.current.setZIndex(map.markers.stores.current.originalZIndex);
            }
            
            /**
             * Update the current store marker, and set it to an active state by:
             *   (1) changing its visual so it stands out, and
             *   (2) setting its z-index so it is always on top
             * If the store has no coordinates, then it will not have a
             * storeMarker. So check for that first before proceeding.
             **/
            map.markers.stores.current = $targetStoreElem.data('storeMarker');

            var thereIsCurrentStore = (typeof map.markers.stores.current !== 'undefined' 
                                      && map.markers.stores.current !== null);

            if(thereIsCurrentStore) {
                map.markers.stores.current.set('labelClass', map.markers.stores.current.originalLabelClass + ' is-active');
			   if(_store.store_marker!='')
				{
					lastid=_store.store_marker;
					jQuery('.gmnoprint').children('img').unbind();
					map.markers.stores.current.setIcon(_store.store_marker);
				}
				else
				{
					lastid='';
                	map.markers.stores.current.setIcon(urls.pathToIcons + urls.pins.active); /* (1) */
				}
					map.markers.stores.current.setZIndex(999); /* (2) */
					setMapCenter(map.markers.stores.current.position);
            }
            
            
            /**
             * Make the currently selected store stand out in the list.
             **/
            $els.storeList.find('.store-locator__infobox').removeClass('is-active');
            $targetStoreElem.addClass('is-active');

            /* Open the map infobox on medium screens and above */
            if(isMediumScreen) {
                if(thereIsCurrentStore) {
                    map.infobox.self.open(map.self, map.markers.stores.current);
                } else {
                    var mapCenter = map.self.getCenter(),
                        fauxMarker = new google.maps.Marker({
                            position : mapCenter
                        });
                    map.infobox.self.open(map.self, fauxMarker);
                }
            }
            
            /* Populate the details to the Current Store */
            var infoboxArray = [$els.storeLocatorInfoBox, $els.mobileStoreLocatorInfobox];

            while(infoboxArray.length) {
				infoboxArray[0].location.html(_store.storeimage);
                infoboxArray[0].self.toggleClass('store-locator__infobox--no-pointer', !thereIsCurrentStore);
                infoboxArray[0].location.html(_store.location);
                infoboxArray[0].address.html(_store.address);
				infoboxArray[0].website.html(_store.website);
				infoboxArray[0].embedvideo.html(_store.embedvideo);
				infoboxArray[0].email.html(_store.email);
				infoboxArray[0].telephone.html(_store.telephone);
				infoboxArray[0].description.html(_store.description);
                infoboxArray[0].operatingHours.html(_store.operatingHours);
                infoboxArray[0].productsServices.html(_store.productsServices);

                if(_store.directions) {
                    infoboxArray[0].directions.attr('href', _store.directions);
                    infoboxArray[0].directions.css('display', '');
                } else {
                    infoboxArray[0].directions.hide();
                }
				
				if(_store.streetview) {
                    infoboxArray[0].streetview.attr('href', _store.streetview);
                    infoboxArray[0].streetview.css('display', '');
                } else {
                    infoboxArray[0].streetview.hide();
                }

                infoboxArray.shift();
            }

            $els.mobileStoreLocatorInfobox.self.addClass('is-shown');
            
            /*
             * On large screens, scroll the window so that the map is at the center of
             * the window. On small screens, scroll the window so that the search bar
             * is at the top of the window.
             */
		if(scroll_setting=='true'){
            if(isLargeScreen) {
                $(window).scrollTop($els.map.offset().top + $els.topHalf.height()*0.5 - $(window).height()*0.25 - $('#mainNav').height());
            } else if(isMediumScreen || thereIsCurrentStore) {
                $(window).scrollTop($els.topHalf.offset().top - $('#mainNav').height());
            } else {
                $(window).scrollTop($els.mobileStoreLocatorInfobox.self.offset().top - $('#mainNav').height());
            }
		}
            /*
            currentStore.$showDirections.attr('href', ['https://maps.google.com/maps?saddr=', mainMarker.getPosition(), '&daddr=', $targetStoreElem.data('storeMarker').getPosition()].join(''));
            */
			
		
		
		
        }

        function setMainMapMarker(latLng, markerTitle) {
            setMapCenter(latLng, true);
        
            if(typeof map.markers.user.self !== 'undefined') map.markers.user.self.setMap(null);
            
            map.markers.user.self = new MarkerWithLabel({
                position: latLng,
                map: map.self,
                animation: google.maps.Animation.DROP,
                title: markerTitle,
                zIndex: 98,
                icon: urls.pathToIcons + urls.pins.skeuomorph
            });
            
            google.maps.event.removeListener(map.markers.user.clicker);

            map.markers.user.clicker = google.maps.event.addListener(map.markers.user.self, 'click', function() {
                setMapCenter(map.markers.user.self.getPosition());
            });
        }
		
		
    
        function setMapCenter(latLng, fitBounds) {
            _map = map.self;
			
			 //patch 2.6 custom map code
				if(ssf_map_code!="" && ssf_map_code!=undefined)
				{
				  _map.setOptions({styles: ssf_map_code});	

				} else {

				if(style_map_color!=""){
					  var styles = [
					  {
						stylers: [
						  { hue: style_map_color },
						  { saturation: 0 },
						  { lightness: 50 },
						  { gamma: 1 },
						]
					  }
					];

					_map.setOptions({styles: styles});
					}
				}
				
            _map.setCenter(latLng);
                
            if(isLargeScreen) _map.panBy(-100, 0);

            if(_map.getZoom() < 13 && fitBounds !== true) _map.setZoom(14);
        }
        
        function translateIntoLetter(index) {
            if(index < 26) {
                switch(index) {
                    case 0:  return 'A'; break;
                    case 1:  return 'B'; break;
                    case 2:  return 'C'; break;
                    case 3:  return 'D'; break;
                    case 4:  return 'E'; break;
                    case 5:  return 'F'; break;
                    case 6:  return 'G'; break;
                    case 7:  return 'H'; break;
                    case 8:  return 'I'; break;
                    case 9:  return 'J'; break;
                    case 10: return 'K'; break;
                    case 11: return 'L'; break;
                    case 12: return 'M'; break;
                    case 13: return 'N'; break;
                    case 14: return 'O'; break;
                    case 15: return 'P'; break;
                    case 16: return 'Q'; break;
                    case 17: return 'R'; break;
                    case 18: return 'S'; break;
                    case 19: return 'T'; break;
                    case 20: return 'U'; break;
                    case 21: return 'V'; break;
                    case 22: return 'W'; break;
                    case 23: return 'X'; break;
                    case 24: return 'Y'; break;
                    case 25: return 'Z'; break;
                }
            } else {
                return '' + (index-25);
            }
        }

        function distanceAscendingSorter(a, b) {
            var distA = a.distance,
                distB = b.distance;

            if(distA === 'nope') distA = 9999999;
            if(distB === 'nope') distB = 9999999;
            return (distA - distB);
        }
        
        /* Function to get the distance between two sets of Google LatLng objects. */
        function distHaversine(p1, p2) {
            var R = 6371; // earth's mean radius in km
            var dLat  = rad(p2.lat() - p1.lat());
            var dLong = rad(p2.lng() - p1.lng());

            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) * Math.sin(dLong/2) * Math.sin(dLong/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            var d = R * c;

            return d.toFixed(3);
        }

        /* Convert unit to radian */
        function rad(x) {
            return x*Math.PI/180;
        }

        function getText($obj) {
            if(!$obj.length) return '';
            return $.trim($obj.text());
        }

        function locationNotAvailable() {
            map.status.notify({
                message : 'notAllowedUserLocation',
                closeable : true
            });

            $('<a href="#/" class="inline-space-left inline-space-right">Continue anyway</a>')
                .appendTo(map.status.$label)
                .on('click', function(e) {
                    e.preventDefault();
                    map.status.conceal();
                });

            map.status.$label.append('|');

            $('<a href="#/" class="inline-space-left inline-space-right">Share my location</a>')
                .appendTo(map.status.$label)
                .on('click', function(e) {
                    e.preventDefault();
                    geolocator.watch();
                });
        }
    };

    function setupMapStatus() {
        $.extend(map.status, {
            /*
             * Possible settings:

                map.status.notify({
                    message: (String),
                    closeable: (Implicit Boolean),
                    loadingIndicator: (Implicit Boolean),
                    autoclose: (Implicit Boolean)
                });

             * All can be included/excluded as necessary.
             */
            notify : function(settings) {
                var self = this;

                clearTimeout(self.timer);

                if(settings.message) {
                    var statusMessage = self.messages[settings.message] || settings.message;
                    self.$label.find('a').off();
                    self.$label.html(statusMessage);
                }

                self.$el.toggleClass('is-loading', Boolean(settings.loadingIndicator));
                self.$el.toggleClass('is-closeable', Boolean(settings.closeable));

                self.reveal();

                if(settings.autoclose) {
                    self.timer = setTimeout(function() {
                        self.conceal();
                    }, self.duration);
                }
            },

            reveal : function() {
                var $statusEl = this.$el,
                    targetHeight;

                if($statusEl.hasClass('is-shown')) return;

                $statusEl.css('height', 'auto');
                targetHeight = $statusEl.height();
                $statusEl
                    .css('height', '')
                    .addClass('is-shown');

                setTimeout(function() {
                    $statusEl
                        .off(FE.events.transitionEnd)
                        .on(FE.events.transitionEnd, function() {
                            $(this)
                                .off(FE.events.transitionEnd)
                                .removeClass('is-transitionable')
                                .css('height', 'auto');
                        })
                        .addClass('is-transitionable')
                        .css('height', targetHeight + 'px');
                }, 5);
            },

            conceal : function() {
                var $statusEl = map.status.$el;

                $statusEl
                    .css('height', $statusEl.height())
                    .removeClass('is-shown');

                setTimeout(function() {
                    $statusEl
                        .off(FE.events.transitionEnd)
                        .on(FE.events.transitionEnd, function() {
                            $(this)
                                .off(FE.events.transitionEnd)
                                .removeClass('is-transitionable');
                        })
                        .addClass('is-transitionable')
                        .css('height', '');
                }, 5);
            }
        });
    }
	
	

});

function streetView(lat,lng){
    if(scroll_setting=='true'){
        $("body").animate({scrollTop:ssf_scroll_top}, 'slow');
     }
		   // street view
		   street = new google.maps.StreetViewPanorama(document.getElementById("storeLocatorMap"), { 

			position: new google.maps.LatLng(lat, lng),
			zoomControl: false,
			enableCloseButton: true,
			addressControl: false,
			panControl: true,
			linksControl: true
		  });

		}

		
		var base64 = {};
base64.PADCHAR = '=';
base64.ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

base64.makeDOMException = function() {
    // sadly in FF,Safari,Chrome you can't make a DOMException
    var e, tmp;

    try {
        return new DOMException(DOMException.INVALID_CHARACTER_ERR);
    } catch (tmp) {
        // not available, just passback a duck-typed equiv
        // https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Objects/Error
        // https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Objects/Error/prototype
        var ex = new Error("DOM Exception 5");

        // ex.number and ex.description is IE-specific.
        ex.code = ex.number = 5;
        ex.name = ex.description = "INVALID_CHARACTER_ERR";

        // Safari/Chrome output format
        ex.toString = function() { return 'Error: ' + ex.name + ': ' + ex.message; };
        return ex;
    }
}

base64.getbyte64 = function(s,i) {
    // This is oddly fast, except on Chrome/V8.
    //  Minimal or no improvement in performance by using a
    //   object with properties mapping chars to value (eg. 'A': 0)
    var idx = base64.ALPHA.indexOf(s.charAt(i));
    if (idx === -1) {
        throw base64.makeDOMException();
    }
    return idx;
}

base64.decode = function(s) {
    // convert to string
    s = '' + s;
    var getbyte64 = base64.getbyte64;
    var pads, i, b10;
    var imax = s.length
    if (imax === 0) {
        return s;
    }

    if (imax % 4 !== 0) {
        throw base64.makeDOMException();
    }

    pads = 0
    if (s.charAt(imax - 1) === base64.PADCHAR) {
        pads = 1;
        if (s.charAt(imax - 2) === base64.PADCHAR) {
            pads = 2;
        }
        // either way, we want to ignore this last block
        imax -= 4;
    }

    var x = [];
    for (i = 0; i < imax; i += 4) {
        b10 = (getbyte64(s,i) << 18) | (getbyte64(s,i+1) << 12) |
            (getbyte64(s,i+2) << 6) | getbyte64(s,i+3);
        x.push(String.fromCharCode(b10 >> 16, (b10 >> 8) & 0xff, b10 & 0xff));
    }

    switch (pads) {
    case 1:
        b10 = (getbyte64(s,i) << 18) | (getbyte64(s,i+1) << 12) | (getbyte64(s,i+2) << 6);
        x.push(String.fromCharCode(b10 >> 16, (b10 >> 8) & 0xff));
        break;
    case 2:
        b10 = (getbyte64(s,i) << 18) | (getbyte64(s,i+1) << 12);
        x.push(String.fromCharCode(b10 >> 16));
        break;
    }
    return x.join('');
}

base64.getbyte = function(s,i) {
    var x = s.charCodeAt(i);
    if (x > 255) {
        throw base64.makeDOMException();
    }
    return x;
}

base64.encode = function(s) {
    if (arguments.length !== 1) {
        throw new SyntaxError("Not enough arguments");
    }
    var padchar = base64.PADCHAR;
    var alpha   = base64.ALPHA;
    var getbyte = base64.getbyte;

    var i, b10;
    var x = [];

    // convert to string
    s = '' + s;

    var imax = s.length - s.length % 3;

    if (s.length === 0) {
        return s;
    }
    for (i = 0; i < imax; i += 3) {
        b10 = (getbyte(s,i) << 16) | (getbyte(s,i+1) << 8) | getbyte(s,i+2);
        x.push(alpha.charAt(b10 >> 18));
        x.push(alpha.charAt((b10 >> 12) & 0x3F));
        x.push(alpha.charAt((b10 >> 6) & 0x3f));
        x.push(alpha.charAt(b10 & 0x3f));
    }
    switch (s.length - imax) {
    case 1:
        b10 = getbyte(s,i) << 16;
        x.push(alpha.charAt(b10 >> 18) + alpha.charAt((b10 >> 12) & 0x3F) +
               padchar + padchar);
        break;
    case 2:
        b10 = (getbyte(s,i) << 16) | (getbyte(s,i+1) << 8);
        x.push(alpha.charAt(b10 >> 18) + alpha.charAt((b10 >> 12) & 0x3F) +
               alpha.charAt((b10 >> 6) & 0x3f) + padchar);
        break;
    }
    return x.join('');
}
function SendMail(rcvEmail)
{
	var emailRegex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;;
	var name = document.form.ssf_cont_name.value,
	email = document.form.ssf_cont_email.value,
	phone = document.form.ssf_cont_phone.value,
	message = document.form.ssf_cont_msg.value;
	var storename =jQuery('#storeLocatorInfobox .store-location').html();
	var rcvEmail=rcvEmail;
	document.getElementById("ssf-msg-status").innerHTML ="";
	
		if(name == "" )
		{
			document.form.ssf_cont_name.focus() ;
			document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>Please enter your "+ssf_cont_us_name+"</span>"
			return false;
		}
		if(email == "" )
		{
			document.form.ssf_cont_email.focus() ;
			document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>Please enter your "+ssf_cont_us_email+"</span>";
			return false;
		}
		else if(!emailRegex.test(email)){
		  document.form.ssf_cont_email.focus();
		  document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>Please enter an correct "+ssf_cont_us_email+" </span>";
		  return false;
		  }
		if(message == "" )
		{
			document.form.ssf_cont_msg.focus() ;
			document.getElementById("ssf-msg-status").innerHTML = "<span style='color:red;'>Please enter your "+ssf_cont_us_msg+"  </span>";
			return false;
		}
	   jQuery.ajax
		({
		type: "POST",
		url: 'sendMail.php?t='+d.getTime(),
		data: {name: name, email: email, phone: phone, message:message, rcvEmail: rcvEmail,subject: storename},
		cache: false,
		success: function (html)
		{
			 document.getElementById("ssf-contact-form").reset();
			 document.getElementById("ssf-msg-status").innerHTML = "<span style='color:green;' id='imageMsgAlert'>"+ssf_msg_sucess+"</span>";
			 jQuery('#imageMsgAlert').fadeOut(5000);
		}
	});
}
function ssf_ifrane_vedio(){
	var jQueryallVideos = jQuery("iframe[src^='http']");

					jQueryallVideos.each(function() {

					jQuery(this)

						.data('aspectRatio', this.height / this.width)

						.removeAttr('height')

						.removeAttr('width');

					});

					var newWidth = "100%";

					jQueryallVideos.each(function() {

						var jQueryel = jQuery(this);

						jQueryel

							.width(newWidth)

							.height(newWidth);

					});
	
}

/* pagination code here */
var number_of_pages
function pagging(ssf_pagination){
	var show_per_page = ssf_pagination; 
	var number_of_items = $('#storeLocator__storeList').children().size();
	number_of_pages = Math.ceil(number_of_items/show_per_page);
	$('#current_page').val(0);
	$('#show_per_page').val(show_per_page);
	$('#storeLocator__currentStoreCount').html(number_of_items);
	if(number_of_pages>1)
	{
	var navigation_html = '<a class="previous_link arrow-toggler-left pagination-btn paginationgrey" id="ssf_previous_link" href="javascript:previous();">&nbsp; &nbsp; '+ssf_prev_label+'</a>';
	var current_link = 0;
	while(number_of_pages > current_link){
		navigation_html += '<a class="page_link" style="display:none;" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';
		current_link++;
	}
	navigation_html += '<a class="next_link arrow-toggler-right pagination-btn button" id="ssf_next_link" href="javascript:next();">'+ssf_next_label+' &nbsp; &nbsp;</a>';
	}else { var navigation_html=''; }
	$('#page_navigation').html(navigation_html);
	$('#page_navigation .page_link:first').addClass('active_page');
	$('#storeLocator__storeList').children().css('display', 'none');
	$('#storeLocator__storeList').children().slice(0, show_per_page).css('display', 'block');

}

function previous(){
	
	new_page = parseInt($('#current_page').val()) - 1;
	if($('.active_page').prev('.page_link').length==true){
		go_to_page(new_page);
	}
	
}

function next(){
	new_page = parseInt($('#current_page').val()) + 1;
	if($('.active_page').next('.page_link').length==true){
		go_to_page(new_page);
	}
	
}
function go_to_page(page_num){
   
	var show_per_page = parseInt($('#show_per_page').val());
	if(page_num==number_of_pages-1){
		$('#ssf_next_link').addClass('paginationgrey');
		$('#ssf_next_link').removeClass('button');
	}
	else{
		$('#ssf_next_link').removeClass('paginationgrey');
		$('#ssf_next_link').addClass('button');
	}
	if(page_num>0){
		$('#ssf_previous_link').removeClass('paginationgrey');
		$('#ssf_previous_link').addClass('button');
	}
	else{
	 $('#ssf_previous_link').addClass('paginationgrey');
	 $('#ssf_previous_link').removeClass('button');
	}
	start_from = page_num * show_per_page;
	end_on = start_from + show_per_page;
	$('#storeLocator__storeList').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');
	$('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');
	$('#current_page').val(page_num);
	$('#storeLocator__storeList').heightSyncify({
                    items : ['.infobox__body']
                });
				
				$('html, body').animate({ 'scrollTop' : $("#storeLocator__storeList").position().top + 250 });
}

$(".info__toggler").on("click", function(e){
var id= this.id;
if ($('#'+id).hasClass('actives'))
	{
		$('.info__toggler').addClass('actives');
		$('#'+id).removeClass('actives');
	}
	else{
		$('.info__toggler').addClass('actives');
		
	}

});