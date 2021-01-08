define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: [__('Sales'), __('Orders')]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Orderdata.column
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: __('Sales'),
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Orderdata.paydata
                },
                    {
                        name: __('Orders'),
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {}
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderdata.createdata
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
                Orderdata.column.push((new Date()).toLocaleTimeString().replace(/^\D*/, ''));
                var amount = Math.floor(Math.random() * 200) + 20;
                Orderdata.createdata.push(amount);
                Orderdata.paydata.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Orderdata.column.length >= 20) {
                    //移除最开始的一条数据
                    Orderdata.column.shift();
                    Orderdata.paydata.shift();
                    Orderdata.createdata.shift();
                }
                myChart.setOption({
                    xAxis: {
                        data: Orderdata.column
                    },
                    series: [{
                        name: __('Sales'),
                        data: Orderdata.paydata
                    },
                        {
                            name: __('Orders'),
                            data: Orderdata.createdata
                        }]
                });
                if ($("#echart").width() != $("#echart canvas").width() && $("#echart canvas").width() < $("#echart").width()) {
                    myChart.resize();
                }
            }, 2000);
            $(window).resize(function () {
                myChart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });

            //读取FastAdmin的更新信息和社区动态
            $.ajax({
                url: Config.fastadmin.api_url + '/news/index',
                type: 'post',
                dataType: 'jsonp',
                success: function (ret) {
                    $("#news-list").html(Template("newstpl", {news: ret.newslist}));
                }
            });
            $.ajax({
                url: Config.fastadmin.api_url + '/forum/discussion',
                type: 'post',
                dataType: 'jsonp',
                success: function (ret) {
                    $("#discussion-list").html(Template("discussiontpl", {news: ret.discussionlist}));
                }
            });
        },


        orderstatistics: function () {
            require(['bootstrap-datetimepicker'], function () {
                var options = {
                    format: 'YYYY-MM-DD HH:mm:ss',
                    icons: {
                        time: 'fa fa-clock-o',
                        date: 'fa fa-calendar',
                        up: 'fa fa-chevron-up',
                        down: 'fa fa-chevron-down',
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right',
                        today: 'fa fa-history',
                        clear: 'fa fa-trash',
                        close: 'fa fa-remove'
                    },
                    showTodayButton: true,
                    showClose: true
                };
                $('.datetimepicker').parent().css('position', 'relative');
                $('.datetimepicker').datetimepicker(options);
				
				var myDate = new Date;
				var year = myDate.getFullYear();//获取当前年
				var yue = myDate.getMonth()+1;//获取当前月
				var date = myDate.getDate();//获取当前日
				var stime=year+'-'+yue+'-'+date;
				
                $('#start_time_one').on('dp.change', function(){
					$('#line_chart').show();
                    start_time = $('#start_time_one').val();
                    // end_time_one   = $('#end_time_one').val();
                    var myChart = echarts.init(document.getElementById('report_one11111111111'));
                    $.get('/admin/Dashboard/report_one?start_time='+start_time).done(function (data) {
                        // 折线图
                        option = {
                            // title: {
                            //     text: '折线图堆叠'
                            // },
                            tooltip: {
                                trigger: 'axis'
                            },
                            legend: {
                                data:['总数量','总金额']
                            },
                            grid: {
                                left: '3%',
                                right: '4%',
                                bottom: '3%',
                                containLabel: true
                            },
                            toolbox: {
                                feature: {
                                    saveAsImage: {}
                                }
                            },
                            xAxis: {
                                type: 'category',
                                boundaryGap: false,
                                data: data.date
                            },
                            yAxis: {
                                type: 'value'
                            },
                            series: data.data
                        };

                        myChart.setOption(option,true);
                    });
				});
				report_two(stime,stime)
				function report_two(start_time,end_time){
					var orderratio = echarts.init(document.getElementById('report_two'));
					 $.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
					     var dete=[];
					    for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
					    }
						option = {
					        tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
					    };
					    orderratio.setOption(option,true);
					});
				}
				report_three(stime,stime)
				function report_three(start_time,end_time){
					var receiving = echarts.init(document.getElementById('report_three'));
					 $.get('/admin/Dashboard/report_three?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
						}
						option = {
					        tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
					    };
					    receiving.setOption(option,true);
					});
				}
				report_four(stime,stime)
				function report_four(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_four'));
					 $.get('/admin/Dashboard/report_four?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						 console.log(data);
						 var dete=[];
						 for(i=0;i<data.length;i++){
							 dete[i]={value:data[i].value,name:data[i].name};
						 }
					    option = {
					        tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
					    };
					    myChart.setOption(option,true);
					});
				}
				report_five(stime,stime)
				function report_five(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_five'));
						 $.get('/admin/Dashboard/report_five?start_time='+start_time+'&end_time='+end_time).done(function (data) {
							var dete=[],dctc=[];
							for(i=0;i<data.length;i++){
								dete[i]=data[i].name;
								dctc[i]=data[i].value;
							}
							option = {
								xAxis: {
									type: 'category',
									data:dete,
								},
								yAxis: {
									type: 'value'
								},
								color:['#999966'],
								series: [{
									data: dctc,
									type: 'bar'
								}],
								label: {
									show: true, 
									position: 'top', 
									textStyle: { 
										color: 'black',
										fontSize: 16
									}
								},
							};
						myChart.setOption(option,true);
					});
				}
				report_six(stime,stime)
				function report_six(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_six'));
					 $.get('/admin/Dashboard/report_six?start_time='+start_time+'&end_time='+end_time).done(function (data) {
					    var dete=[];
					    for(i=0;i<data.length;i++){
					    	dete[i]={value:data[i].value,name:data[i].name};
					    }
						option = {
					        tooltip : {
					    			trigger: 'item',
					    			formatter: "{a} <br/>{b} : {c} ({d}%)"
					    	},
					    	color:['#336699','#cc9966'],
					    	series : [
					    			{
					    			name: '访问来源',
					    			type: 'pie',
					    			radius: '60%',
					    			data:dete,
					    			label: { 
					    				normal: {
					    					show: true, 
					    					position: 'inner'
					    				}
					    			},
					    			itemStyle: {
					    				borderWidth:5,
					    				borderColor:'#fff',
					    			}
					    		}
					    	]
					    };
					    myChart.setOption(option,true);
					});
				}
				report_seven(stime,stime)
				function report_seven(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_seven'));
					 $.get('/admin/Dashboard/report_seven?start_time='+start_time+'&end_time='+end_time).done(function (data) {
					    var dete=[];
					    for(i=0;i<data.length;i++){
					    	dete[i]={value:data[i].value,name:data[i].name};
					    }
						option = {
					        tooltip : {
					    			trigger: 'item',
					    			formatter: "{a} <br/>{b} : {c} ({d}%)"
					    	},
					    	color:['#cc6666','#ff9900'],
					    	series : [
					    			{
					    			name: '访问来源',
					    			type: 'pie',
					    			radius: '60%',
					    			data:dete,
					    			label: { 
					    				normal: {
					    					show: true, 
					    					position: 'inner'
					    				}
					    			},
					    			itemStyle: {
					    				borderWidth:5,
					    				borderColor:'#fff',
					    			}
					    		}
					    	]
					    };
					    myChart.setOption(option,true);
					});
				}
				$('#polling_two').click(function(){
					var start_time=$('#start_time_two').val(),
						end_time=$('#end_time_two').val();
					report_two(start_time,end_time)
				})
				$('#polling_three').click(function(){
					var start_time=$('#start_time_three').val(),
						end_time=$('#end_time_three').val();
					report_three(start_time,end_time)
				})
				$('#polling_four').click(function(){
					var start_time=$('#start_time_four').val(),
						end_time=$('#end_time_four').val();
					report_four(start_time,end_time)
				})
				$('#polling_five').click(function(){
					var start_time=$('#start_time_five').val(),
						end_time=$('#end_time_five').val();
					report_five(start_time,end_time)
				})
				$('#polling_six').click(function(){
					var start_time=$('#start_time_six').val(),
						end_time=$('#end_time_six').val();
					report_six(start_time,end_time)
				})
				$('#polling_seven').click(function(){
					var start_time=$('#start_time_seven').val(),
						end_time=$('#end_time_seven').val();
					report_seven(start_time,end_time)
				})
			});
            // $(document).on("click", "#time111111111111", function () {
            //     console.log('123');
            // });
            

           

        },
		
		userstatistics:function(){
			 require(['bootstrap-datetimepicker'], function () {
			    var options = {
			        format: 'YYYY-MM-DD HH:mm:ss',
			        icons: {
			            time: 'fa fa-clock-o',
			            date: 'fa fa-calendar',
			            up: 'fa fa-chevron-up',
			            down: 'fa fa-chevron-down',
			            previous: 'fa fa-chevron-left',
			            next: 'fa fa-chevron-right',
			            today: 'fa fa-history',
			            clear: 'fa fa-trash',
			            close: 'fa fa-remove'
			        },
			        showTodayButton: true,
			        showClose: true
			    };
			    $('.datetimepicker').parent().css('position', 'relative');
			    $('.datetimepicker').datetimepicker(options);
				
				var myDate = new Date;
				var year = myDate.getFullYear();//获取当前年
				var yue = myDate.getMonth()+1;//获取当前月
				var date = myDate.getDate();//获取当前日
				var stime=year+'-'+yue+'-'+date;
				report_one(stime,stime)
				function report_one(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_one'));
					 $.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
					     var dete=[];
					    for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
					    }
						option = {
					        tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
					    };
					    myChart.setOption(option,true);
					});
				}
				report_two(stime,stime)
				function report_two(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_two'));
					$.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
						}
						option = {
							tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
						};
						myChart.setOption(option,true);
					});
				}
				report_three(stime,stime)
				function report_three(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_three'));
						 $.get('/admin/Dashboard/report_five?start_time='+start_time+'&end_time='+end_time).done(function (data) {
							var dete=[],dctc=[];
							for(i=0;i<data.length;i++){
								dete[i]=data[i].name;
								dctc[i]=data[i].value;
							}
							option = {
								xAxis: {
									type: 'category',
									data:dete,
								},
								yAxis: {
									type: 'value'
								},
								color:['#999966'],
								series: [{
									data: dctc,
									type: 'bar'
								}],
								label: {
									show: true, 
									position: 'top', 
									textStyle: { 
										color: 'black',
										fontSize: 16
									}
								},
							};
						myChart.setOption(option,true);
					});
				}
				report_four(stime,stime)
				function report_four(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_four'));
					$.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
						}
						option = {
							tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
						};
						myChart.setOption(option,true);
					});
				}
				report_five(stime,stime)
				function report_five(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_five'));
					$.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
						}
						option = {
							tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
						};
						myChart.setOption(option,true);
					});
				}
				report_six(stime,stime)
				function report_six(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_six'));
					$.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
						}
						option = {
							tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
						};
						myChart.setOption(option,true);
					});
				}
				report_seven(stime,stime)
				function report_seven(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_seven'));
					$.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
						}
						option = {
							tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
						};
						myChart.setOption(option,true);
					});
				}
				report_eight(stime,stime)
				function report_eight(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_eight'));
						 $.get('/admin/Dashboard/report_five?start_time='+start_time+'&end_time='+end_time).done(function (data) {
							var dete=[],dctc=[];
							for(i=0;i<data.length;i++){
								dete[i]=data[i].name;
								dctc[i]=data[i].value;
							}
							option = {
								xAxis: {
									type: 'category',
									data:dete,
								},
								yAxis: {
									type: 'value'
								},
								color:['#999966'],
								series: [{
									data: dctc,
									type: 'bar'
								}],
								label: {
									show: true, 
									position: 'top', 
									textStyle: { 
										color: 'black',
										fontSize: 16
									}
								},
							};
						myChart.setOption(option,true);
					});
				}
				report_nine(stime,stime)
				function report_nine(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_nine'));
						 $.get('/admin/Dashboard/report_five?start_time='+start_time+'&end_time='+end_time).done(function (data) {
							var dete=[],dctc=[];
							for(i=0;i<data.length;i++){
								dete[i]=data[i].name;
								dctc[i]=data[i].value;
							}
							option = {
								xAxis: {
									type: 'category',
									data:dete,
								},
								yAxis: {
									type: 'value'
								},
								color:['#999966'],
								series: [{
									data: dctc,
									type: 'bar'
								}],
								label: {
									show: true, 
									position: 'top', 
								textStyle: { 
									color: 'black',
									fontSize: 16
								}
							},
						};
						myChart.setOption(option,true);
					});
				}
				report_ten(stime,stime)
				function report_ten(start_time,end_time){
					var myChart = echarts.init(document.getElementById('report_ten'));
					$.get('/admin/Dashboard/report_two?start_time='+start_time+'&end_time='+end_time).done(function (data) {
						var dete=[],
							dctc=[];
						for(i=0;i<data.length;i++){
							dete[i]={value:data[i].value,name:data[i].name};
							dctc[i]=data[i].name;
						}
						option = {
							tooltip : {
									trigger: 'item',
									formatter: "{a} <br/>{b} : {c} ({d}%)"
							},
							color:['#336633','#006666'],
								legend: {
								orient: 'vertical',
								right:'right',
								top:'40%',
								data: dctc,
							},
							series : [
									{
									name: '访问来源',
									type: 'pie',
									radius: '60%',
									center: ['25%', '50%'],
									data:dete,
									label: { 
										normal: {
											show: true, 
											position: 'inner'
										}
									},
									itemStyle: {
										borderWidth:5,
										borderColor:'#fff',
									}
								}
							]
						};
						myChart.setOption(option,true);
					});
				}
			});
		}
    };

    return Controller;
});