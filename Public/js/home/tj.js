$(function(){
    $.post(pv,{id:id});
    $.post(uv,{id:id});
    $.post(ROOT+'/index.php/Similarity/calculateSimilarity',{});
});