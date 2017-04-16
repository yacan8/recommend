
var force = function(nodes,edges,container) {

   var width = $(container).empty().width();
   var height = 1000;


   var svg = d3.select(container)
       .append("svg")
       .attr("width",width)
       .attr("height",height);

   var force = d3.layout.force()
       .nodes(nodes)		//指定节点数组
       .links(edges)		//指定连线数组
       .size([width,height])	//指定范围
       .linkDistance(150)	//指定连线长度
       .charge(-400);	//相互之间的作用力

   force.start();	//开始作用
   //添加连线
   var svg_edges = svg.selectAll("line")
       .data(edges)
       .enter()
       .append("line")
       .style("stroke","#ccc")
       .style("stroke-width",1);


   //添加节点
   var svg_nodes = svg.selectAll("g")
       .data(nodes)
       .enter()
       .append("g")
       .on('dblclick',function(d,i){
          console.log(d);
       })
       .call(force.drag);	//使得节点能够拖动
   svg_nodes.append("image")
       .attr("xlink:href", function(d){
          var result;
          if(d.type == 1) {
             d.icon =  d.icon ?  d.icon : 'default.jpg';
             result = DATAPATH +'/login_thumb/'+ d.icon;
          } else if ( d.type == 2 ) {
             result = PUBLIC + '/img/keyword_svg.png';
          } else {
             result = PUBLIC + '/img/type_svg.png';
          }
          return result;
       })
       .attr("x", function(d){
          var result;
          if(d.type == 1) {
             result = -20 ;
          } else if ( d.type == 2 ) {
             result = -10;
          } else {
             result = -15;
          }
          return result;
       })
       .attr("y", function(d){
          var result;
          if(d.type == 1) {
             result = -20 ;
          } else if ( d.type == 2 ) {
             result = -10;
          } else {
             result = -15;
          }
          return result;
       })
       .attr("width", function(d){
          var result;
          if(d.type == 1) {
             result = 40 ;
          } else if ( d.type == 2 ) {
             result = 20;
          } else {
             result = 30;
          }
          return result;
       })
       .attr("height", function(d){
          var result;
          if(d.type == 1) {
             result = 40 ;
          } else if ( d.type == 2 ) {
             result = 20;
          } else {
             result = 30;
          }
          return result;
       });

   //添加描述节点的文字
   var svg_texts = svg.selectAll("text")
       .data(nodes)
       .enter()
       .append("text")
       .style("fill", "#777")
       .attr("dx", 12)
       .attr("dy", 8)
       .text(function(d){
          var text ;
          if ( d.type == 1 ) {
             text = d.nickname;
          } else if (d.type == 2) {
             text = d.keyword;
          } else {
             text = d.type_text;
          }
          return text;
       });


   force.on("tick", function(){	//对于每一个时间间隔

      //更新连线坐标
      svg_edges.attr("x1",function(d){ return d.source.x; })
          .attr("y1",function(d){ return d.source.y; })
          .attr("x2",function(d){ return d.target.x; })
          .attr("y2",function(d){ return d.target.y; });

      //更新节点坐标
      // svg_nodes.attr("cx",function(d){ return d.x; })
      //     .attr("cy",function(d){ return d.y; });
      svg_nodes.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

      //更新文字坐标
      svg_texts.attr("x", function(d){ return d.x; })
          .attr("y", function(d){ return d.y; });
   });
};
var getNodesAndEdges = function(data){
   var nodes = [];
   var edges = [];
   var typeMap = {};
   var keywordMap = {};
   data.forEach(function(userItem){
      var userNode = {
         nickname : userItem.nickname,
         icon : userItem.icon,
         last_modify_time : userItem.last_modify_time,
         user_id : userItem.user_id,
         type : 1
      };
      nodes.push(userNode);
      var userNodeIndex = nodes.indexOf(userNode);


      userItem.portrayal.forEach(function(item){
         if( item.id ){
            var nodeIndex = -1;
            var contentMap = item.type == 1 ? keywordMap : typeMap;
            if( !contentMap.hasOwnProperty(item.id)){
               var node = {id:item.id};
               if ( item.type == 1 ) {
                  node.keyword = item.keyword;
                  node.type = 2;

               } else {
                  node.type_text = item.type_text;
                  node.type = 3;
               }
               nodes.push(node);
               nodeIndex = nodes.length - 1;
               if ( item.type == 1 ) {
                  keywordMap[item.id] = nodeIndex;
               } else {
                  typeMap[item.id] = nodeIndex;
               }
            } else {
               if ( item.type == 1 ) {
                  nodeIndex = keywordMap[item.id];
               } else {
                  nodeIndex = typeMap[item.id];
               }
            }
            edges.push({source:userNodeIndex,target:nodeIndex});
         }
      })
   });
   return {
      nodes:nodes,
      edges:edges
   }
};
function content_loading(url,params,beforeSend){
   var params = Object.keys(params).map(function(key){return key+'='+params[key]}).join('&');
   if(beforeSend){beforeSend();}
   window.ajax_loading = true;
   return fetch(url+'?'+params, {credentials: "include"}).then(function(res){window.ajax_loading = false;return res.json()});
}
$(function(){
   $('#userShowContainer').on('click','.user-similarity-item',function(){
      $(this).toggleClass('active');
      var user = [];
      $('.user-similarity-item.active').each(function(){
         var user_id = $(this).attr('data-id');
         window.userSimilarityInfo.forEach(function (item) {
            if( user_id == item.user_id) {
               user.push(item);
            }
         })
      })
      var dataObject = getNodesAndEdges(user);
      force(dataObject.nodes,dataObject.edges,'#similarityShow');

   });



   content_loading(ROOT+'/index.php/Admin/Similarity/userSimilarityLoad',{id:id}).then(function(result){
      console.log(result);


      if( result.success ){
         window.userSimilarityInfo = result.attr;
         var userShowHtml =  '';
         result.attr.forEach(function(item){
            item.ROOT = ROOT;
            item.DATAPATH = DATAPATH;
            item.PUBLIC = PUBLIC;
            item.similarity = item.similarity ?'相似度:'+ ((item.similarity*100)+'').substr(0,5)+'%' : '查询者';
            userShowHtml += template('userSimilarityShow',item);
         });
         $('#userShowContainer').html(userShowHtml);


         var dataObject = getNodesAndEdges(result.attr);
         force(dataObject.nodes,dataObject.edges,'#similarityShow');

      }

   })
});