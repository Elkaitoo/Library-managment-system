function calcprice(price , period, c) {
    if (period == 1){
        document.getElementById("price" + c).innerHTML = (price *1 ).toFixed(3) + " BHD";
    }else if (period == 2){
        document.getElementById("price" + c).innerHTML = (price *4).toFixed(3) + " BHD";
    }else if (period == 3){
        document.getElementById("price" + c).innerHTML = (price *24 ).toFixed(3)+ " BHD" ;
    }
   
}