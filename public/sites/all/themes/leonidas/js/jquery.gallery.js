(function($) {
	$.fn.gallery = function(options) {
		var args = Array.prototype.slice.call(arguments);
		args.shift();
		this.each(function(){
			if(this.galControl && typeof options === 'string') {
				if(typeof this.galControl[options] === 'function') {
					this.galControl[options].apply(this.galControl, args);
				}
			} else {
				this.galControl = new Gallery(this, options);
			}
		});
		return this;
	};
	function Gallery(context, options) { this.init(context, options); };
	Gallery.prototype = {
		options:{},
		init: function (context, options){
			this.options = $.extend({
				duration: 700,
				slideElement:1,
				autoRotation: false,
				effect: false,
				listOfSlides: '.list > li',
				switcher: false,
				autoSwitcher: false,
				disableBtn: false,
				nextBtn: 'a.link-next, a.btn-next, a.next',
				prevBtn: 'a.link-prev, a.btn-prev, a.prev',
				circle: true,
				clone: false,
				direction: false,
				event: 'click'
			}, options || {});
			var self = this;
			this.context = $(context);
			this.els = this.context.find(this.options.listOfSlides);
			this.list = this.els.parent();
			this.count = this.els.length;
			this.autoRotation = this.options.autoRotation;
			this.direction = this.options.direction;
			this.duration = this.options.duration;
			if (this.options.clone) {
				this.list.append(this.els.clone());
				this.list.prepend(this.els.clone());
				this.els = this.context.find(this.options.listOfSlides);
			}
			this.wrap = this.list.parent();
			if (this.options.nextBtn) this.nextBtn = this.context.find(this.options.nextBtn);
			if (this.options.prevBtn) this.prevBtn = this.context.find(this.options.prevBtn);

			this.calcParams(this);
			
			if (this.options.autoSwitcher) {
				this.switcherHolder = this.context.find(this.options.switcher).empty();
				this.switchPattern = $('<ul class="'+ (this.options.autoSwitcher == true ? '' : this.options.autoSwitcher) +'"></ul>');
				for (var i=0;i<this.max+1;i++){
					$('<li><a href="#">'+i+'</a></li>').appendTo(this.switchPattern);
				}
				this.switchPattern.appendTo(this.switcherHolder);
				this.switcher = this.context.find(this.options.switcher).find('li');
				this.active = 0;
			} else {
				if (this.options.switcher) {
					this.switcher = this.context.find(this.options.switcher);
					this.active = this.switcher.index(this.switcher.filter('.active:eq(0)'));
				}
				else this.active = this.els.index(this.els.filter('.active:eq(0)'));
			}
			if (this.active < 0) this.active = 0;
			this.last = this.active;
			if (this.options.switcher) this.switcher.removeClass('active').eq(this.active).addClass('active');
			if (this.options.clone) this.active += this.count;
			
			if (this.options.effect) this.els.css({opacity: 0}).removeClass('active').eq(this.active).addClass('active').css({opacity: 1}).css('opacity', 'auto');
			else {
				if (this.direction) this.list.css({marginTop: -(this.mas[this.active])});
				else this.list.css({marginLeft: -(this.mas[this.active])});
			}
			
			
			if (this.options.nextBtn) this.initEvent(this, this.nextBtn,true);
			if (this.options.prevBtn) this.initEvent(this, this.prevBtn,false);
			
			this.initWindow(this,$(window));
			
			if (this.autoRotation) this.runTimer(this);
			
			if (this.options.switcher) this.initEventSwitcher(this, this.switcher);
			if (this.options.disableBtn && !this.options.circle && !this.options.clone) this.disableControls();
		},
		calcParams: function(self){
			this.mas = [];
			this.sum = 0;
			this.max = this.count-1;
			this.width = 0;
			this.els.each(function(){self.mas.push(self.width);self.width += self.direction?$(this).outerHeight(true):$(this).outerWidth(true);self.sum+=self.direction?$(this).outerHeight(true):$(this).outerWidth(true);});
			this.finish = this.direction?this.sum-this.wrap.outerHeight():this.sum-this.wrap.outerWidth();
			for (var i=0;i<this.count;i++){
				if (this.mas[i]>=this.finish) {
					this.max = i;
					break;
				}
			}
		},
		changeSettings: function(set,val){
			this[set] = val;
		},
		fadeElement: function(){
			this.els.eq(this.last).animate({opacity:0}, {queue:false, duration: this.duration});
			this.els.removeClass('active').eq(this.active).addClass('active').animate({
				opacity:1
			}, {queue:false, duration: this.duration, complete: function(){
				$(this).css('opacity','auto');
			}});
			if (this.options.switcher) this.switcher.removeClass('active').eq(this.active).addClass('active');
			this.last = this.active;
		},
		scrollElement: function(f){
			if (this.direction) this.list.animate({marginTop: f ? -this.finish : -(this.mas[this.active])}, {queue:false, duration: this.duration});
			else this.list.animate({marginLeft: f ? -this.finish : -(this.mas[this.active])}, {queue:false, duration: this.duration});
			if (this.options.switcher) this.switcher.removeClass('active').eq(this.options.clone ? this.active < this.count ? this.active/this.options.slideElement : this.active >= this.count*2 ? (this.active - this.count*2)/this.options.slideElement : (this.active - this.count)/this.options.slideElement : this.active/this.options.slideElement).addClass('active');
		},
		runTimer: function($this){
			if($this._t) clearTimeout($this._t);
			$this._t = setInterval(function(){
				$this.nextStep();
			}, this.autoRotation);
		},
		initEventSwitcher: function($this, el){
			el.bind($this.options.event, function(){
				if (!$(this).hasClass('active')){
					$this.active = $this.switcher.index($(this)) * $this.options.slideElement;
					if ($this.options.clone) $this.active += $this.count;
					$this.initMove();
				}
				return false;
			});
		},
		initEvent: function($this, addEventEl, dir){
			addEventEl.bind($this.options.event, function(){
				if (dir) $this.nextStep();
				else $this.prevStep();
				if($this._t) clearTimeout($this._t);
				if ($this.autoRotation) $this.runTimer($this);
				return false;
			});
		},
		disableControls: function(){
			this.prevBtn.removeClass(this.options.disableBtn);
			this.nextBtn.removeClass(this.options.disableBtn);
			if (this.active>=this.max) this.nextBtn.addClass(this.options.disableBtn);
			if (this.active<=0) this.prevBtn.addClass(this.options.disableBtn);
		},
		initMove: function(){
			var f = false;
			if (this.active >= this.max && !this.options.clone) {
				f = true;
				this.active = this.max;
			}
			if(this._t) clearTimeout(this._t);
			if (!this.options.effect) this.scrollElement(f);
			else this.fadeElement();
			if (this.autoRotation) this.runTimer(this);
			if (this.options.disableBtn && !this.options.circle && !this.options.clone) this.disableControls();
		},
		nextStep:function(){
			var f = false;
			this.active = this.active + this.options.slideElement;
			if (this.options.disableBtn && !this.options.circle && !this.options.clone) this.disableControls();
			if (this.options.clone){
				if (this.active > this.count*2) {
					if (this.direction) this.list.css({marginTop:-this.mas[this.count]});
					else this.list.css({marginLeft:-this.mas[this.count]});
					this.active = this.count+this.options.slideElement;
				}
			} else {
				if (this.active >= this.max) {
					if (this.options.circle) {
						if (this.active > this.max) this.active = 0;
						else {
							this.active = this.max;
							f = true
						}
					}
					else {
						this.active = this.max;
						f = true;
					}
				}
			}
			if (!this.options.effect) this.scrollElement(f);
			else this.fadeElement();
		},
		prevStep: function(){
			var f = false;
			this.active = this.active - this.options.slideElement;
			if (this.options.disableBtn && !this.options.circle && !this.options.clone) this.disableControls();
			if (this.options.clone){
				if (this.active < 0) {
					if (this.direction) this.list.css({marginTop:-this.mas[this.count]});
					else this.list.css({marginLeft:-this.mas[this.count]});
					this.active = this.count-1;
				}
			} else {
				if (this.active < 0) {
					if (this.options.circle) {
						this.active = this.max;
						f = true;
					}
					else this.active = 0;
				}
			}
			if (!this.options.effect) this.scrollElement(f);
			else this.fadeElement();
		},
		initWindow: function($this,$window){
			$window.focus($.proxy(this.play,this));
			$window.blur($.proxy(this.stop,this));
		},
		stop: function(){
			if (this._t) clearTimeout(this._t);
		},
		play: function(){
			if (this._t) clearTimeout(this._t);
			if (this.autoRotation) this.runTimer(this);
		}
	}
}(jQuery));

/*
 * jQuery Carousel plugin
 */
;(function($){
	function ScrollGallery(options) {
		this.options = $.extend({
			mask: 'div.mask',
			slider: '>*',
			slides: '>*',
			activeClass:'active',
			disabledClass:'disabled',
			btnPrev: 'a.btn-prev',
			btnNext: 'a.btn-next',
			generatePagination: false,
			pagerList: '<ul>',
			pagerListItem: '<li><a href="#"></a></li>',
			pagerListItemText: 'a',
			pagerLinks: '.pagination li',
			currentNumber: 'span.current-num',
			totalNumber: 'span.total-num',
			btnPlay: '.btn-play',
			btnPause: '.btn-pause',
			btnPlayPause: '.btn-play-pause',
			autorotationActiveClass: 'autorotation-active',
			autorotationDisabledClass: 'autorotation-disabled',
			circularRotation: true,
			disableWhileAnimating: false,
			autoRotation: false,
			pauseOnHover: isTouchDevice ? false : true,
			maskAutoSize: false,
			switchTime: 4000,
			animSpeed: 600,
			event:'click',
			swipeGap: false,
			swipeThreshold: 50,
			handleTouch: true,
			vertical: false,
			useTranslate3D: false,
			step: false
		}, options);
		this.init();
	}
	ScrollGallery.prototype = {
		init: function() {
			if(this.options.holder) {
				this.findElements();
				this.attachEvents();
				this.refreshPosition();
				this.refreshState(true);
				this.resumeRotation();
				this.makeCallback('onInit', this);
			}
		},
		findElements: function() {
			// define dimensions proporties
			this.fullSizeFunction = this.options.vertical ? 'outerHeight' : 'outerWidth';
			this.innerSizeFunction = this.options.vertical ? 'height' : 'width';
			this.slideSizeFunction = 'outerHeight';
			this.maskSizeProperty = 'height';
			this.animProperty = this.options.vertical ? 'marginTop' : 'marginLeft';
			this.swipeProperties = this.options.vertical ? ['up', 'down'] : ['left', 'right'];
			
			// control elements
			this.gallery = $(this.options.holder);
			this.mask = this.gallery.find(this.options.mask);
			this.slider = this.mask.find(this.options.slider);
			this.slides = this.slider.find(this.options.slides);
			this.btnPrev = this.gallery.find(this.options.btnPrev);
			this.btnNext = this.gallery.find(this.options.btnNext);
			this.currentStep = 0; this.stepsCount = 0;
			
			// get start index
			if(this.options.step === false) {
				var activeSlide = this.slides.filter('.'+this.options.activeClass);
				if(activeSlide.length) {
					this.currentStep = this.slides.index(activeSlide);
				}
			}
			
			// calculate offsets
			this.calculateOffsets();
			$(window).bind('resize orientationchange', $.proxy(this.onWindowResize, this));
			
			// create gallery pagination
			if(typeof this.options.generatePagination === 'string') {
				this.buildPagination();
			} else {
				this.pagerLinks = this.gallery.find(this.options.pagerLinks);
				this.attachPaginationEvents();
			}
			
			// autorotation control buttons
			this.btnPlay = this.gallery.find(this.options.btnPlay);
			this.btnPause = this.gallery.find(this.options.btnPause);
			this.btnPlayPause = this.gallery.find(this.options.btnPlayPause);
			
			// misc elements
			this.curNum = this.gallery.find(this.options.currentNumber);
			this.allNum = this.gallery.find(this.options.totalNumber);
		},
		attachEvents: function() {
			this.btnPrev.bind(this.options.event, this.bindScope(function(e){
				this.prevSlide();
				e.preventDefault();
			}));
			this.btnNext.bind(this.options.event, this.bindScope(function(e){
				this.nextSlide();
				e.preventDefault();
			}));
			
			// pause on hover handling
			if(this.options.pauseOnHover) {
				this.gallery.hover(this.bindScope(function(){
					if(this.options.autoRotation) {
						this.galleryHover = true;
						this.pauseRotation();
					}
				}), this.bindScope(function(){
					if(this.options.autoRotation) {
						this.galleryHover = false;
						this.resumeRotation();
					}
				}));
			}
			
			// autorotation buttons handler
			this.btnPlay.bind(this.options.event, this.bindScope(this.startRotation));
			this.btnPause.bind(this.options.event, this.bindScope(this.stopRotation));
			this.btnPlayPause.bind(this.options.event, this.bindScope(function(){
				if(!this.gallery.hasClass(this.options.autorotationActiveClass)) {
					this.startRotation();
				} else {
					this.stopRotation();
				}
			}));
			
			// swipe event handling
			if(isTouchDevice) {
				// enable hardware acceleration
				if(this.options.useTranslate3D) {
					this.slider.css({'-webkit-transform': 'translate3d(0px, 0px, 0px)'});
				}
				
				// swipe gestures
				if(this.options.handleTouch && $.fn.swipe) {
					this.mask.swipe({
						threshold: this.options.swipeThreshold,
						allowPageScroll: 'vertical',
						swipeStatus: $.proxy(function(e, phase, direction, distance) {
							if(phase === 'start') {
								this.originalOffset = parseInt(this.slider.stop(true, false).css(this.animProperty));
							} else if(phase === 'move') {
								if(direction === this.swipeProperties[0] || direction === this.swipeProperties[1]) {
									var tmpOffset = this.originalOffset + distance * (direction === this.swipeProperties[0] ? -1 : 1);
									if(!this.options.swipeGap) {
										tmpOffset = Math.max(Math.min(0, tmpOffset), this.maxOffset);
									}
									this.tmpProps = {};
									this.tmpProps[this.animProperty] = tmpOffset;
									this.slider.css(this.tmpProps);
									e.preventDefault();
								}
							} else if(phase === 'cancel') {
								// return to previous position
								this.switchSlide();
							}
						},this),
						swipe: $.proxy(function(event, direction) {
							if(direction === this.swipeProperties[0]) {
								if(this.currentStep === this.stepsCount - 1) this.switchSlide();
								else this.nextSlide();
							} else if(direction === this.swipeProperties[1]) {
								if(this.currentStep === 0) this.switchSlide();
								else this.prevSlide();
							}
						},this)
					});
				}
			}
		},
		onWindowResize: function() {
			if(!this.galleryAnimating) {
				this.calculateOffsets();
				this.refreshPosition();
				this.buildPagination();
				this.refreshState();
				this.resizeQueue = false;
			} else {
				this.resizeQueue = true;
			}
		},
		refreshPosition: function() {
			this.currentStep = Math.min(this.currentStep, this.stepsCount - 1);
			this.tmpProps = {};
			this.tmpProps[this.animProperty] = this.getStepOffset();
			this.slider.stop().css(this.tmpProps);
		},
		calculateOffsets: function() {
			this.maskSize = this.mask[this.innerSizeFunction]();
			this.sumSize = this.getSumSize();
			this.maxOffset = this.maskSize - this.sumSize;
			
			// vertical gallery with single size step custom behavior
			if(this.options.vertical && this.options.maskAutoSize) {
				this.options.step = 1;
				this.stepsCount = this.slides.length;
				this.stepOffsets = [0];
				var tmpOffset = 0;
				for(var i = 0; i < this.slides.length; i++) {
					tmpOffset -= $(this.slides[i])[this.fullSizeFunction](true);
					this.stepOffsets.push(tmpOffset);
				}
				this.maxOffset = tmpOffset;
				return;
			}
			
			// scroll by slide size
			if(typeof this.options.step === 'number' && this.options.step > 0) {
				this.slideDimensions = [];
				this.slides.each($.proxy(function(ind, obj){
					this.slideDimensions.push( $(obj)[this.fullSizeFunction](true) );
				},this));
				
				// calculate steps count
				this.stepOffsets = [0];
				this.stepsCount = 1;
				var tmpOffset = 0, tmpStep = 0;
				while(tmpOffset > this.maxOffset) {
					tmpOffset -= this.getSlideSize(tmpStep, tmpStep + this.options.step);
					tmpStep += this.options.step;
					this.stepOffsets.push(Math.max(tmpOffset, this.maxOffset));
					this.stepsCount++;
				}
			}
			// scroll by mask size
			else {
				// define step size
				this.stepSize = this.maskSize;
				
				// calculate steps count
				this.stepsCount = 1;
				var tmpOffset = 0;
				while(tmpOffset > this.maxOffset) {
					tmpOffset -= this.stepSize;
					this.stepsCount++;
				}
			}
		},
		getSumSize: function() {
			var sum = 0;
			this.slides.each($.proxy(function(ind, obj){
				sum += $(obj)[this.fullSizeFunction](true);
			},this));
			this.slider.css(this.innerSizeFunction, sum);
			return sum;
		},
		getStepOffset: function(step) {
			step = step || this.currentStep;
			if(typeof this.options.step === 'number') {
				return this.stepOffsets[this.currentStep];
			} else {
				return Math.max(-this.currentStep * this.stepSize, this.maxOffset);
			}
		},
		getSlideSize: function(i1, i2) {
			var sum = 0;
			for(var i = i1; i < Math.min(i2, this.slideDimensions.length); i++) {
				sum += this.slideDimensions[i];
			}
			return sum;
		},
		buildPagination: function() {
			if(this.options.generatePagination) {
				this.pagerHolder = this.gallery.find(this.options.generatePagination).empty();
				this.pagerList = $(this.options.pagerList).appendTo(this.pagerHolder);
				for(var i = 0; i < this.stepsCount; i++) {
					$(this.options.pagerListItem).appendTo(this.pagerList).find(this.options.pagerListItemText).text(i+1);
				}
				this.pagerLinks = this.pagerList.children();
				this.attachPaginationEvents();
			}
		},
		attachPaginationEvents: function() {
			this.pagerLinks.each(this.bindScope(function(ind, obj){
				$(obj).bind(this.options.event, this.bindScope(function(){
					this.numSlide(ind);
					return false;
				}));
			}));
		},
		prevSlide: function() {
			if(!(this.options.disableWhileAnimating && this.galleryAnimating)) {
				if(this.currentStep > 0) {
					this.currentStep--;
					this.switchSlide();
				} else if(this.options.circularRotation) {
					this.currentStep = this.stepsCount - 1;
					this.switchSlide();
				}
			}
		},
		nextSlide: function(fromAutoRotation) {
			if(!(this.options.disableWhileAnimating && this.galleryAnimating)) {
				if(this.currentStep < this.stepsCount - 1) {
					this.currentStep++;
					this.switchSlide();
				} else if(this.options.circularRotation || fromAutoRotation === true) {
					this.currentStep = 0;
					this.switchSlide();
				}
			}
		},
		numSlide: function(c) {
			if(this.currentStep != c) {
				this.currentStep = c;
				this.switchSlide();
			}
		},
		switchSlide: function() {
			this.galleryAnimating = true;
			this.tmpProps = {}
			this.tmpProps[this.animProperty] = this.getStepOffset();
			this.slider.stop().animate(this.tmpProps,{duration: this.options.animSpeed, complete: this.bindScope(function(){
				// animation complete
				this.galleryAnimating = false;
				if(this.resizeQueue) {
					this.onWindowResize();
				}
				
				// onchange callback
				this.makeCallback('onChange', this);
				this.autoRotate();
			})});
			this.refreshState();
			
			// onchange callback
			this.makeCallback('onBeforeChange', this);
		},
		refreshState: function(initial) {
			if(this.options.step === 1 || this.stepsCount === this.slides.length) {
				this.slides.removeClass(this.options.activeClass).eq(this.currentStep).addClass(this.options.activeClass);
			}
			this.pagerLinks.removeClass(this.options.activeClass).eq(this.currentStep).addClass(this.options.activeClass);
			this.curNum.html(this.currentStep+1);
			this.allNum.html(this.stepsCount);
			
			// initial refresh
			if(this.options.maskAutoSize && typeof this.options.step === 'number') {
				this.tmpProps = {};
				this.tmpProps[this.maskSizeProperty] = this.slides.eq(Math.min(this.currentStep,this.slides.length-1))[this.slideSizeFunction](true);
				this.mask.stop()[initial ? 'css' : 'animate'](this.tmpProps);
			}
			
			// disabled state
			if(!this.options.circularRotation) {
				this.btnPrev.add(this.btnNext).removeClass(this.options.disabledClass);
				if(this.currentStep === 0) this.btnPrev.addClass(this.options.disabledClass);
				if(this.currentStep === this.stepsCount - 1) this.btnNext.addClass(this.options.disabledClass);
			}
		},
		startRotation: function() {
			this.options.autoRotation = true;
			this.galleryHover = false;
			this.autoRotationStopped = false;
			this.resumeRotation();
		},
		stopRotation: function() {
			this.galleryHover = true;
			this.autoRotationStopped = true;
			this.pauseRotation();
		},
		pauseRotation: function() {
			this.gallery.addClass(this.options.autorotationDisabledClass);
			this.gallery.removeClass(this.options.autorotationActiveClass);
			clearTimeout(this.timer);
		},
		resumeRotation: function() {
			if(!this.autoRotationStopped) {
				this.gallery.addClass(this.options.autorotationActiveClass);
				this.gallery.removeClass(this.options.autorotationDisabledClass);
				this.autoRotate();
			}
		},
		autoRotate: function() {
			clearTimeout(this.timer);
			if(this.options.autoRotation && !this.galleryHover && !this.autoRotationStopped) {
				this.timer = setTimeout(this.bindScope(function(){
					this.nextSlide(true);
				}), this.options.switchTime);
			} else {
				this.pauseRotation();
			}
		},
		bindScope: function(func, scope) {
			return $.proxy(func, scope || this);
		},
		makeCallback: function(name) {
			if(typeof this.options[name] === 'function') {
				var args = Array.prototype.slice.call(arguments);
				args.shift();
				this.options[name].apply(this, args);
			}
		}
	}
	
	// detect device type
	var isTouchDevice = (function() {
		try {
			return ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;
		} catch (e) {
			return false;
		}
	}());
	
	// jquery plugin
	$.fn.scrollGallery = function(opt){
		return this.each(function(){
			$(this).data('ScrollGallery', new ScrollGallery($.extend(opt,{holder:this})));
		});
	}
}(jQuery));

/*
 * touchSwipe - jQuery Plugin
 * http://plugins.jquery.com/project/touchSwipe
 * http://labs.skinkers.com/touchSwipe/
 *
 * Copyright (c) 2010 Matt Bryson (www.skinkers.com)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * $version: 1.2.5
 */
;(function(a){a.fn.swipe=function(c){if(!this){return false}var k={fingers:1,threshold:75,swipe:null,swipeLeft:null,swipeRight:null,swipeUp:null,swipeDown:null,swipeStatus:null,click:null,triggerOnTouchEnd:true,allowPageScroll:"auto"};var m="left";var l="right";var d="up";var s="down";var j="none";var u="horizontal";var q="vertical";var o="auto";var f="start";var i="move";var h="end";var n="cancel";var t="ontouchstart" in window,b=t?"touchstart":"mousedown",p=t?"touchmove":"mousemove",g=t?"touchend":"mouseup",r="touchcancel";var e="start";if(c.allowPageScroll==undefined&&(c.swipe!=undefined||c.swipeStatus!=undefined)){c.allowPageScroll=j}if(c){a.extend(k,c)}return this.each(function(){var D=this;var H=a(this);var E=null;var I=0;var x={x:0,y:0};var A={x:0,y:0};var K={x:0,y:0};function z(N){var M=t?N.touches[0]:N;e=f;if(t){I=N.touches.length}distance=0;direction=null;if(I==k.fingers||!t){x.x=A.x=M.pageX;x.y=A.y=M.pageY;if(k.swipeStatus){y(N,e)}}else{C(N)}D.addEventListener(p,J,false);D.addEventListener(g,L,false)}function J(N){if(e==h||e==n){return}var M=t?N.touches[0]:N;A.x=M.pageX;A.y=M.pageY;direction=v();if(t){I=N.touches.length}e=i;G(N,direction);if(I==k.fingers||!t){distance=B();if(k.swipeStatus){y(N,e,direction,distance)}if(!k.triggerOnTouchEnd){if(distance>=k.threshold){e=h;y(N,e);C(N)}}}else{e=n;y(N,e);C(N)}}function L(M){M.preventDefault();distance=B();direction=v();if(k.triggerOnTouchEnd){e=h;if((I==k.fingers||!t)&&A.x!=0){if(distance>=k.threshold){y(M,e);C(M)}else{e=n;y(M,e);C(M)}}else{e=n;y(M,e);C(M)}}else{if(e==i){e=n;y(M,e);C(M)}}D.removeEventListener(p,J,false);D.removeEventListener(g,L,false)}function C(M){I=0;x.x=0;x.y=0;A.x=0;A.y=0;K.x=0;K.y=0}function y(N,M){if(k.swipeStatus){k.swipeStatus.call(H,N,M,direction||null,distance||0)}if(M==n){if(k.click&&(I==1||!t)&&(isNaN(distance)||distance==0)){k.click.call(H,N,N.target)}}if(M==h){if(k.swipe){k.swipe.call(H,N,direction,distance)}switch(direction){case m:if(k.swipeLeft){k.swipeLeft.call(H,N,direction,distance)}break;case l:if(k.swipeRight){k.swipeRight.call(H,N,direction,distance)}break;case d:if(k.swipeUp){k.swipeUp.call(H,N,direction,distance)}break;case s:if(k.swipeDown){k.swipeDown.call(H,N,direction,distance)}break}}}function G(M,N){if(k.allowPageScroll==j){M.preventDefault()}else{var O=k.allowPageScroll==o;switch(N){case m:if((k.swipeLeft&&O)||(!O&&k.allowPageScroll!=u)){M.preventDefault()}break;case l:if((k.swipeRight&&O)||(!O&&k.allowPageScroll!=u)){M.preventDefault()}break;case d:if((k.swipeUp&&O)||(!O&&k.allowPageScroll!=q)){M.preventDefault()}break;case s:if((k.swipeDown&&O)||(!O&&k.allowPageScroll!=q)){M.preventDefault()}break}}}function B(){return Math.round(Math.sqrt(Math.pow(A.x-x.x,2)+Math.pow(A.y-x.y,2)))}function w(){var P=x.x-A.x;var O=A.y-x.y;var M=Math.atan2(O,P);var N=Math.round(M*180/Math.PI);if(N<0){N=360-Math.abs(N)}return N}function v(){var M=w();if((M<=45)&&(M>=0)){return m}else{if((M<=360)&&(M>=315)){return m}else{if((M>=135)&&(M<=225)){return l}else{if((M>45)&&(M<135)){return s}else{return d}}}}}try{this.addEventListener(b,z,false);this.addEventListener(r,C)}catch(F){}})}})(jQuery);