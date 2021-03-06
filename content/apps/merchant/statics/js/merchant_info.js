// JavaScript Document
; (function (app, $) {
    app.merchant_info = {
        init: function () {
            app.merchant_info.submit_form();
            app.merchant_info.ajaxremove();
            app.merchant_info.identity_type();
            app.merchant_info.region_change();
            app.merchant_info.gethash();
            $('.disable input,.disable select, .disable textarea, .disable .btn').attr('disabled', 'disabled');
            $('.nodisabled').attr('disabled', false);
            app.merchant_info.image_preview();
            app.merchant_info.orders_auto_confirm_change();
            app.merchant_info.make_thumb();
        },

        submit_form: function () {
            var $this = $('form[name="theForm"]');
            var option = {
                rules: {
                },
                messages: {

                },
                submitHandler: function () {
                    $this.ajaxSubmit({
                        dataType: "json",
                        success: function (data) {
                            ecjia.merchant.showmessage(data);
                        }
                    });
                }
            }
            var options = $.extend(ecjia.merchant.defaultOptions.validate, option);
            $this.validate(options);
        },

        ajaxremove: function () {
            $('[data-toggle="ajax_remove"]').on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    url = $this.attr('data-href') || $this.attr('href'),
                    msg = $this.attr('data-msg') || js_lang.do_this;
                if (!url) {
                    smoke.alert(js_lang.parameter_error);
                    return false;
                }
                smoke.confirm(msg, function (e) {
                    if (e) {
                        $.get(url, function (data) {
                        	ecjia.merchant.showmessage(data);
                        }, 'json');
                    }
                }, { ok: js_lang.ok, cancel: js_lang.cancel });
            });
        },

        identity_type: function () {
            $("input[name='identity_type']").on('change', function () {
                var $this = $(this),
                    val = $this.val();
                val = (val == 1) ? 1 : 2;
                if (val == 1) {
                    $('.identity_type').addClass('hide');
                } else {
                    $('.identity_type').removeClass('hide');
                }
            });
        },

        region_change: function () {
            $('select[data-toggle="regionSummary"]').on('change', function (e) {
                if ($('.form-address').hasClass('error')) $('.form-address').removeClass('error');
                var $this = $(this);
                var url = $this.attr('data-url') || $this.attr('href') || $('select[data-toggle="regionSummary"]').eq(0).attr('data-url');
                var pid = $this.attr('data-pid') || $this.find('option:checked').val();
                var type = $this.attr('data-type');
                var target = $this.attr('data-target');
                var option = { url: url, pid: pid, type: type, target: target };
                e.preventDefault();
                app.merchant_info.regionSummary(option);
            });
        },

        regionSummary: function (options) {
            if (!options.url) {
                console.log(js_lang.address_source_specified);
                return;
            }

            var defaults = {
                pid: 0,
                type: 1,
                target: 'region-summary'
            }

            var options = $.extend({}, defaults, options);
            this.url = options.url ? options.url : this.url;
            app.merchant_info.loadRegions(options.pid, options.type, options.target);
        },

        loadRegions: function (parent, type, target) {
            $.get(this.url, 'type=' + type + '&target=' + target + "&parent=" + parent, app.merchant_info.response, "JSON");
        },

        response: function (result) {
            var sel = $('.' + result.target);
            sel.find('option').eq(0).attr('checked', 'checked');
            sel.find('option:gt(0)').remove();

            if (result.regions) {
                for (i = 0; i < result.regions.length; i++) {
                    var opt = document.createElement("OPTION");
                    opt.value = result.regions[i].region_id;
                    opt.text = result.regions[i].region_name;
                    sel.append(opt);
                }
            }
            sel.trigger("liszt:updated").trigger("change");
        },

        formatTimeLabelFunc: function (value, type) {
            var hours = String(value).substr(0, 2);
            var mins = String(value).substr(3, 2);

            if (hours >= 24) {
                hours = hours - 24;
                hours = (hours < 10 ? "0" + hours : hours);
                value = hours + ':' + mins;
                var text = String(js_lang.next_day + '%s').replace('%s', value);
                return text;
            }
            else {
                return value;
            }
        },

        range: function () {
            $('.range-slider').jRange({
                from: 0, to: 2880, step: 30,
                scale: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', js_lang.next_day + '00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                format: app.merchant_info.formatTimeLabelFunc,
                width: 600,
                showLabels: true,
                isRange: true
            });
        },

        gethash: function () {
            $('[data-toggle="get-gohash"]').on('click', function (e) {
                e.preventDefault();
                var province = $('select[name="province"]').val(),
                    city = $('select[name="city"]').val(),
                    district = $('select[name="district"]').val(),
                    address = $('input[name="address"]').val(),
                    street = $('select[name="street"]').val(), 
                    url = $(this).attr('data-url');

                var option = {
                    'province': 　province,
                    'city': 　city,
                    'district': 　district,
                    'street': street,
                    'address': address,
                }
                $.post(url, option, app.merchant_info.sethash, "JSON");
            })
        },

        sethash: function (location) {
            if (location.state == 'error') {
                if (location.element == 'address') {
                    $('input[name="address"]').focus();
                    $('input[name="address"]').parents('.form-group').addClass('f_error');
                } else {
                    $('.form-address').addClass('error');
                }
                ecjia.merchant.showmessage(location);
            } else {
                $('.location-address').removeClass('hide');
                var map, markersArray = [];
                var lat = location.result.location.lat;
                var lng = location.result.location.lng;
                var latLng = new qq.maps.LatLng(lat, lng);
                var map = new qq.maps.Map(document.getElementById("allmap"), {
                    center: latLng,
                    zoom: 16
                });
                $('input[name="longitude"]').val(lng);
                $('input[name="latitude"]').val(lat);
                setTimeout(function () {
                    var marker = new qq.maps.Marker({
                        position: latLng,
                        map: map
                    });
                    markersArray.push(marker);
                }, 500);
                //添加监听事件 获取鼠标单击事件
                qq.maps.event.addListener(map, 'click', function (event) {
                    if (markersArray) {
                        for (i in markersArray) {
                            markersArray[i].setMap(null);
                        }
                        markersArray.length = 0;
                    }
                    $('input[name="longitude"]').val(event.latLng.lng)
                    $('input[name="latitude"]').val(event.latLng.lat)
                    var marker = new qq.maps.Marker({
                        position: event.latLng,
                        map: map
                    });
                    markersArray.push(marker);
                });

            }
        },
        mh_switch: function () {
            $('#danger-toggle-button').toggleButtons({
                style: {
                    enabled: "danger",
                    disabled: "success"
                }
            });
            app.merchant_info.submit_form();

            var InterValObj; 	//timer变量，控制时间
            var count = 120; 	//间隔函数，1秒执行
            var curCount;		//当前剩余秒数

            $("#get_code").on('click', function (e) {
                e.preventDefault();
                var url = $(this).attr('data-url') + '&mobile=' + $("input[name='mobile']").val();
                $.get(url, function (data) {
                    if (!!data && data.state == 'success') {
                        curCount = count;
                        $("#mobile").attr("readonly", "true");
                        $("#get_code").attr("disabled", "true");
                        $("#get_code").html(js_lang.resend + curCount + "(s)");
                        InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
                    }
                    ecjia.merchant.showmessage(data);
                }, 'json');
            });

            //timer处理函数
            function SetRemainTime() {
                if (curCount == 0) {
                    window.clearInterval(InterValObj);		//停止计时器
                    $("#mobile").removeAttr("readonly");	//启用按钮
                    $("#get_code").removeAttr("disabled");	//启用按钮
                    $("#get_code").html(js_lang.resend_code);
                } else {
                    curCount--;
                    $("#get_code").attr("disabled", "true");
                    $("#get_code").html(js_lang.resend + curCount + "(s)");
                }
            };
            $('.unset_SetRemain').on('click', function () {
                window.clearInterval(InterValObj);
            })
        },
        
        image_preview: function() {
        	var initPhotoSwipeFromDOM=function(gallerySelector){var parseThumbnailElements=function(el){var thumbElements=el.childNodes,numNodes=thumbElements.length,items=[],el,childElements,thumbnailEl,size,item;for(var i=0;i<numNodes;i++){el=thumbElements[i];if(el.nodeType!==1){continue}childElements=el.children;size=el.getAttribute('data-size').split('x');item={src:el.getAttribute('href'),w:parseInt(size[0],10),h:parseInt(size[1],10),author:el.getAttribute('data-author')};item.el=el;if(childElements.length>0){item.msrc=childElements[0].getAttribute('src');if(childElements.length>1){item.title=childElements[1].innerHTML}}var mediumSrc=el.getAttribute('data-med');if(mediumSrc){size=el.getAttribute('data-med-size').split('x');item.m={src:mediumSrc,w:parseInt(size[0],10),h:parseInt(size[1],10)}}item.o={src:item.src,w:item.w,h:item.h};items.push(item)}return items};var closest=function closest(el,fn){return el&&(fn(el)?el:closest(el.parentNode,fn))};var onThumbnailsClick=function(e){e=e||window.event;e.preventDefault?e.preventDefault():e.returnValue=false;var eTarget=e.target||e.srcElement;var clickedListItem=closest(eTarget,function(el){return el.tagName==='A'});if(!clickedListItem){return}var clickedGallery=clickedListItem.parentNode;var childNodes=clickedListItem.parentNode.childNodes,numChildNodes=childNodes.length,nodeIndex=0,index;for(var i=0;i<numChildNodes;i++){if(childNodes[i].nodeType!==1){continue}if(childNodes[i]===clickedListItem){index=nodeIndex;break}nodeIndex++}if(index>=0){openPhotoSwipe(index,clickedGallery)}return false};var photoswipeParseHash=function(){var hash=window.location.hash.substring(1),params={};if(hash.length<5){return params}var vars=hash.split('&');for(var i=0;i<vars.length;i++){if(!vars[i]){continue}var pair=vars[i].split('=');if(pair.length<2){continue}params[pair[0]]=pair[1]}if(params.gid){params.gid=parseInt(params.gid,10)}return params};var openPhotoSwipe=function(index,galleryElement,disableAnimation,fromURL){var pswpElement=document.querySelectorAll('.pswp')[0],gallery,options,items;items=parseThumbnailElements(galleryElement);options={galleryUID:galleryElement.getAttribute('data-pswp-uid'),getThumbBoundsFn:function(index){var thumbnail=items[index].el.children[0],pageYScroll=window.pageYOffset||document.documentElement.scrollTop,rect=thumbnail.getBoundingClientRect();return{x:rect.left,y:rect.top+pageYScroll,w:rect.width}},addCaptionHTMLFn:function(item,captionEl,isFake){if(!item.title){captionEl.children[0].innerText='';return false}captionEl.children[0].innerHTML=item.title+'<br/><small>Photo: '+item.author+'</small>';return true}};if(fromURL){if(options.galleryPIDs){for(var j=0;j<items.length;j++){if(items[j].pid==index){options.index=j;break}}}else{options.index=parseInt(index,10)-1}}else{options.index=parseInt(index,10)}if(isNaN(options.index)){return}var radios=document.getElementsByName('gallery-style');for(var i=0,length=radios.length;i<length;i++){if(radios[i].checked){if(radios[i].id=='radio-all-controls'){}else if(radios[i].id=='radio-minimal-black'){options.mainClass='pswp--minimal--dark';options.barsSize={top:0,bottom:0};options.captionEl=false;options.fullscreenEl=false;options.shareEl=false;options.bgOpacity=0.85;options.tapToClose=true;options.tapToToggleControls=false}break}}if(disableAnimation){options.showAnimationDuration=0}gallery=new PhotoSwipe(pswpElement,PhotoSwipeUI_Default,items,options);var realViewportWidth,useLargeImages=false,firstResize=true,imageSrcWillChange;gallery.listen('beforeResize',function(){var dpiRatio=window.devicePixelRatio?window.devicePixelRatio:1;dpiRatio=Math.min(dpiRatio,2.5);realViewportWidth=gallery.viewportSize.x*dpiRatio;if(realViewportWidth>=1200||(!gallery.likelyTouchDevice&&realViewportWidth>800)||screen.width>1200){if(!useLargeImages){useLargeImages=true;imageSrcWillChange=true}}else{if(useLargeImages){useLargeImages=false;imageSrcWillChange=true}}if(imageSrcWillChange&&!firstResize){gallery.invalidateCurrItems()}if(firstResize){firstResize=false}imageSrcWillChange=false});gallery.listen('gettingData',function(index,item){if(useLargeImages){item.src=item.o.src;item.w=item.o.w;item.h=item.o.h}else{item.src=item.m.src;item.w=item.m.w;item.h=item.m.h}});gallery.init()};var galleryElements=document.querySelectorAll(gallerySelector);for(var i=0,l=galleryElements.length;i<l;i++){galleryElements[i].setAttribute('data-pswp-uid',i+1);galleryElements[i].onclick=onThumbnailsClick}var hashData=photoswipeParseHash();if(hashData.pid&&hashData.gid){openPhotoSwipe(hashData.pid,galleryElements[hashData.gid-1],true,true)}};
        	initPhotoSwipeFromDOM('.img-pwsp-list');
        },
        
        confirm_link: function () {
            $('[data-toggle="confirm_link"]').on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    url = $this.attr('data-href') || $this.attr('href'),
                    msg = $this.attr('data-msg') || js_lang.do_this,
                    ajax = $this.attr('data-ajax') || false;
                if (!url) {
                    smoke.alert(js_lang.parameter_error);
                    return false;
                }
                smoke.confirm(msg, function (e) {
                    if (e) {
                    	if (ajax) {
                    		$.get(url, function (data) {
                            	ecjia.merchant.showmessage(data);
                            }, 'json');
                    	} else {
                    		ecjia.pjax(url);
                    	}
                        
                    }
                }, { ok: js_lang.ok, cancel: js_lang.cancel });
            });
        },
        
        orders_auto_confirm_change: function() {
        	$('input[name="orders_auto_confirm"]').off('click').on('click', function() {
        		var $this = $(this),
        			val = $this.val();
        		if (val == 1) {
        			$('.orders_auto_rejection_time').addClass('hide');
        		} else {
        			$('.orders_auto_rejection_time').removeClass('hide');
        		}
        	});
        },

        make_thumb: function() {
            $('[data-toggle="make_thumb"]').off('click').on('click', function() {
                var $this = $(this),
                    url = $this.attr('data-url'),
                    type = $this.attr('data-type');
                $.post(url, {type: type}, function(data){
                    ecjia.merchant.showmessage(data);
                });
            })
        }
    };
})(ecjia.merchant, jQuery);

//end