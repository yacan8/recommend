var data = [
        {
          qu_no:1,
          title:'猜猜TA是谁-壹七专访',
          qu:'你觉得TA是做什么的？',
          aw:['他是一个摄影爱好者。','他是一个书画爱好者。'],
          true_aw:2,
          false_mes:'你成为读心师的概率为 <span style="color:red">0</span><br>看样子你还是个纯洁的宝宝～<br>要阅历的，多在社会上摸爬滚打几年再来吧！',
          img:PUBLIC+'/img/interview1.jpg'
        },
        {
          qu_no:2,
          title:'我成为读心师的概率有20%，你呢',
          qu:'TA是老师还是学生？',
          aw:['他是书画协会的会长。','他是书画协会的指导老师。'],
          true_aw:1,
          false_mes:'你成为读心师的概率为 <span style="color:red">20%</span>，加油！<br>努力修炼吧！你已经有了成功的苗头了！',
          img:PUBLIC+'/img/interview2.jpg'
        },
        {
          qu_no:3,
          title:'我成为读心师的概率有40%，你呢',
          qu:'下面哪句是TA说过的话？',
          aw:['练字、练画本身就是一种很枯燥的事情。','练字、练画从来不是一种很枯燥的事情。'],
          true_aw:1,
          false_mes:'你成为读心师的概率为<span style="color:red">40%</span>，<br>耐得住性子修炼才能去达到更好的层次！读心也是如此！<br>加油～你离成功不远了！',
          img:PUBLIC+'/img/interview3.jpg'
        },
        {
          qu_no:4,
          title:'我成为读心师的概率有60%，你呢',
          qu:'TA认为书画协会的作用在哪？',
          aw:['改善校园文化氛围，增强书画等国学在学校内的影响力，使学校生活更丰富。','书画社团能够将喜欢书画的人聚集起来，给他们创造一个展示自我的平台，并能让他们坚持下去，更重的是对于书画协会的同学来说乐在其中。'],
          true_aw:2,
          false_mes:'你成为读心师的概率为 <span style="color:red">60%</span><br>希望就在前方！<br>勇敢的少年啊～快去实现奇迹～',
          img:PUBLIC+'/img/interview4.jpg'
        },
        {
          qu_no:5,
          title:'我成为读心师的概率有80%，你呢',
          qu:'TA认为该如何把枯燥的书法变得有趣？',
          aw:['通过办活动、搞比赛、做交流让它变得活跃起来，聚集更多的在书画方面有天赋的人才。','通过给大家定期培训。'],
          true_aw:1,
          false_mes:'你成为读心师的概率为 <span style="color:red">80%</span><br>三短一长选一长。一长一短呢？',
          img:PUBLIC+'/img/interview5.jpg'
        },
        {
          title:'我成为读心师的概率有100%，你呢',
          pass:'恭喜你！<br>你成为读心师的概率为百分之百！<br>你是神算子附体了吗？赶快加入天桥算命团队吧！你一定是最棒的🌚<br>天哪……你别看我别看我～我怕我被你看透。<br>',
          result:'<p>高文鹏，1995年出生，2013年加入西华大学书画协会，现任西华大学书画协会会长。</p><p>西华大学书画协会成立于1990年，分别有传统国画、软笔书法、硬笔书法组成，距今历时26年。</p><p>协会每年的入会人员平均200人。</p><img src="'+PUBLIC+'/img/interview6.jpg"><p>每年参加和组织的活动：美寝送字画、社团文化节、迎新晚会、西华大学游园会（由西华大学所有社团组办并参与）、书画比赛。每年的四到五月份都会定时的与其他学校组办社团交流。2011年西华大学首届“墨缘”书画协会成果图文展示顺利举行。<img src="'+PUBLIC+'/img/interview7.jpg"></p><p><a href="http://news.xhu.edu.cn/news/xywh/2011-05-26/15620.html">http://news.xhu.edu.cn/news/xywh/2011-05-26/15620.html</a>报道网址。</p><p>书画协会每周日下午2点-5点为书画练习。</p><p>获得的奖项：西华大学十佳社团（五星社团）、2014年成都十佳白玉社团、西区高校书画比赛。</p><p>会长高文鹏对社团的评价：练字、练画本身就是一种很枯燥的事情，但是书画社团却能够将喜欢书画的人聚集起来，给他们创造一个展示自我的平台，并能让他们坚持下去，更重的是对于书画协会的同学来说乐在其中。</p><p>对于一件看上去枯燥无聊的事情，我们通过办活动、搞比赛、做交流让它变得活跃起来，聚集更多的在书画方面有天赋的人才，这确实是一件极其有意义和有意思的事情。</p>'
        }
      ];
      function loadImage(url ) {
       var img = new Image(); //创建一个Image对象，实现图片的预下载
       img.src = url;
       
       if(img.complete) { // 如果图片已经存在于浏览器缓存，直接调用回调函数
          $('.wait-mask').hide();
           return; // 直接返回，不用再处理onload事件
          }
       img.onload = function () { //图片下载完毕时异步调用callback函数。
            $('.wait-mask').hide();
        };
      };
      ;$(function(){
        $.extend($.fn, {
          question:function(data){
            $('title').html(data.title);
            var _self = $(this);
            $('.wait-mask').show();
            _self.find('.header-container .img-container img').attr('src',data.img);
            _self.find('.qu-container').html(data.qu);
            loadImage(data.img);
            var str = '';
            for(var i = 0 ;i<data.aw.length;i++){
              str = str +  '<a href="javascript:void(0)" class="aw-item"> ● '+ data.aw[i]+'</a>';
            }
            _self.find('.aw-container').attr('data-t',data.true_aw).attr('data-no',data.qu_no).html(str);
          },

          mes:function(method){
            var _self = $(this);
            if(method == 'show'){
              _self.css("display","block");
              setTimeout(function(){
                  _self.find('.mes-container').addClass('active');
              },50);
            }else if(method == 'hide'){
              _self.find('.mes-container').removeClass('active');
              setTimeout(function(){
                  _self.hide();
              },200);
            }
          },


          viewImg:function(method){
            var _self = $(this);
            if(method == 'show'){
              $('.img-mask').show();
              setTimeout(function(){
                  _self.find('.img-modal').addClass('active');
                  _self.find('.img-close').addClass('active');
              },50);
            }else if(method == 'hide'){
              _self.find('.img-modal').removeClass('active');
              _self.find('.img-close').removeClass('active');
              setTimeout(function(){
                    _self.hide();
              },200);
            }
          }
        });
        
        $('.qu').question(data[0]);
      })

      $(document).bind('click','.aw-item',function(){
        var _self = $(this);
        var index = _self.index();
        var aw_container = _self.parents('.aw-container');
        var t = aw_container.attr('data-t');
        var qu_no = aw_container.attr('data-no');
        if(index+1 == Number(t)){
          if(qu_no != data.length-1)
            $('.qu').question(data[qu_no]);
          else{
            $('.mes-mask .mes-content').html(data[qu_no].pass);
            $('title').html(data[qu_no].title);
            $('.wait-mask').hide();
            var mes_container = $('.mes-mask');
            mes_container.mes('show');
            mes_container.find('.restart').hide();
            mes_container.find('.pass').show();
          }
        }else{
          var mes_container = $('.mes-mask');
          mes_container.find('.mes-container .mes-content').html(data[qu_no-1].false_mes);
          mes_container.mes('show');
        }
      })

      $(document).bind('click','.restart',function(){
        $('.mes-mask').mes('hide');
        $('.qu').question(data[0]);
      })

      $(document).bind('click','.btn-imgview,#img-show',function(){
        var img_src = $('.img-container img').attr('src');
        $('.img-mask').find('.img-modal img').attr('src',img_src);
        $('.img-mask').viewImg('show');
      })
      $(document).bind('click','.img-close',function(){
        $('.img-mask').viewImg('hide');
      })
      $(document).bind('click','.start-btn',function(){
        $('.start').hide();
        $('.qu').addClass('active');
      })
      $(document).bind('click','.pass',function(){
        $('.qu').removeClass('active');
        $('.qu').hide();
        $('.mes-mask').mes('hide');
        $('.result').html(data[data.length-1].result).addClass('active');
      })