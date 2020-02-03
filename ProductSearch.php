    <?php $switch = false;

        if(isset($_GET['server'])){
            $switch = $_GET['server'];
        }

        $_APPID = 'harrymin-ProductS-PRD-e16de56b6-438fa4a5';
        function sendRequest($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            if($output==FALSE){
                echo "cURL ERROR: ".curl_error($ch);
                curl_close($ch);
            }else{
                curl_close($ch);
                echo $output; 
            }
        
        }
    ?>

    <?php if($switch):?>

        <?php
            error_reporting(E_ALL);
            if(isset($_GET['keyword'])){
                //prep
                $itemFilter = 0;
                $valueFilter = 0;
                //building the base
                $query = "https://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0&SECURITY-APPNAME={$_APPID}&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&paginationInput.entriesPerPage=20";

                
                $keyword = $_GET['keyword'];
                $keyword = str_replace(' ', '+', $keyword); // Replaces all spaces with +.

                $query .="&keywords={$keyword}";

                

                $category = $_GET['category'];
                if($category != '0'){
                    $query .= "&categoryId={$category}";
                }

                $zip = $_GET['zip'];
                $query .= "&buyerPostalCode={$zip}";

                $localSearch = $_GET['localSearch'];
                $distance = $_GET['distance'];
                if($localSearch == 'true'){
                    $query .= "&itemFilter({$itemFilter}).name=MaxDistance&itemFilter($itemFilter).value={$distance}";
                    $itemFilter += 1;
                }




                $shipping = array("local"=>$_GET['local'],"free"=>$_GET['free']);
                if($shipping["free"] == 'true'){
                    $query .="&itemFilter({$itemFilter}).name=FreeShippingOnly&itemFilter({$itemFilter}).value={$shipping["free"]}";
                    $itemFilter += 1;
                }
                if($shipping["local"] == 'true'){
                    $query .="&itemFilter({$itemFilter}).name=LocalPickupOnly&itemFilter({$itemFilter}).value={$shipping["local"]}";
                    $itemFilter += 1;
                }



                $query .="&itemFilter({$itemFilter}).name=HideDuplicateItems&itemFilter({$itemFilter}).value=true";
                $itemFilter += 1;


                $condition = array("new"=>$_GET['new'],"used"=>$_GET['used'], "unspecified"=>$_GET['unspecified']);

                
                $query .="&itemFilter({$itemFilter}).name=Condition";
                if($condition["new"]=='true'){
                    $query .= "&itemFilter({$itemFilter}).value({$valueFilter})=New";
                    $valueFilter += 1;
                }
                if($condition["used"]=='true'){
                    $query .= "&itemFilter({$itemFilter}).value({$valueFilter})=Used";
                    $valueFilter += 1;
                }
                if($condition["unspecified"]=='true'){
                    $query .= "&itemFilter({$itemFilter}).value({$valueFilter})=Unspecified";
                }
                if(($condition["new"]=='false') && ($condition["used"]=='false') && ($condition["unspecified"]=='false')){
                    $query .= "&itemFilter({$itemFilter}).value({$valueFilter})=New";
                    $valueFilter += 1;
                    $query .= "&itemFilter({$itemFilter}).value({$valueFilter})=Used";
                    $valueFilter += 1;
                    $query .= "&itemFilter({$itemFilter}).value({$valueFilter})=Unspecified";
                }
                sendRequest($query);
            }elseif(isset($_GET['singleItem'])){
                $item_ID = $_GET['itemID'];
                $query = "http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON&appid={$_APPID}&siteid=0&version=967&ItemID={$item_ID}&IncludeSelector=Description,Details,ItemSpecifics";
                sendRequest($query);
            }elseif(isset($_GET['similarItems'])){
                $item_ID = $_GET['itemID'];
                $query = "http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0&CONSUMER-ID={$_APPID}&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&itemId={$item_ID}&maxResults=8";
                sendRequest($query);
            }
        ?>
    <?php else:?>

<!-- END OF SERVER CODE -->

<!-- FRONT END CODE -->
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title> HomeWork </title>
    
    <style>
        *{
            margin:0;
            padding:0;
        }

        .body,html{
            text-align: center;
        }


        .container{
            text-align: center;
            overflow:hidden;
            margin: 5% 30% 1% 30%;
            background-color: #efefef;
            border: 2px solid #cecece;
        }

        .container .form-container{
            padding: 10px 25px;
            text-align: left;
        }

        .main-form{
            justify-items: center;
        }

        .form-header{
            font-family: "Dai Banna SIL";
            font-style: italic;
            font-size: 30pt;
            font-weight: lighter;
            margin-bottom: 1%;
        }

        .divider{
            height: 1px;
            margin-inline-end: 0.5rem;
            margin-inline-start: 0.5rem;
            background: #d5d5d5;
        }

        .row{
            margin-bottom: 15px;
        }


        .local-search .location-picker{
            list-style-type: none;
            display: inline-block;
            vertical-align: top;
            margin:0px;
            padding-left: 5px;
        }

        .row input[type=checkbox]{
            margin-left: 25px;
        }
        #distance{
            margin-left: 10px;
        }

        .buttons{
            text-align: center;
            margin-top: 15px;
        }

        .itemLink{
            text-decoration: none;
            color: #353535;
            margin-top: 20px;
            cursor: pointer;

        }
        .itemLink:hover{
            color: #9f9f9f;
        }
        label{
            font-weight: bold;
        }
    </style>
    <script>
///GLOBAL VARIABLES
    // holds pointers to the DOM form elements
    let inputs = {};
    // Data To be sent to the server is built using the fields in this object
    let submitInfo = {
        keyword: null, 
        category: 0,
        condition: {new: false, used: false, unspecified: false},
        shipping: {local: false, free: false},
        zip: null,
        currentLocL: null,
        distance: 10
    }

    let serverResponse;
    let currentItems=[];
    let selectedItem;
    let results;

///INITALIZATION CODE: CONTAINS AddFormElements(), loadPage(), getLocation().
    //Sets the pointers in the inputs object to point to DOM elements
    function addFormElemens(){
        results = document.getElementById('results');
        inputs.keyword = document.getElementsByName('keyword')[0];
        inputs.localSearch = document.getElementsByName("nearby")[0];
        inputs.distance = document.getElementsByName("distance")[0];
        inputs.here = document.getElementsByName("center")[0];
        inputs.zip = document.getElementsByName("center")[1];
        inputs.zipCode = document.getElementsByName("zip")[0];
        inputs.submit = document.getElementsByName("submit")[0];
        inputs.category = document.getElementsByName("category")[0];
        inputs.form = document.getElementsByName("firstForm")[0];
        inputs.condition = {new: document.getElementsByName('new')[0],
                            used: document.getElementsByName('used')[0],
                            unspecified: document.getElementsByName('unspecified')[0]};
        inputs.pickup = {local: document.getElementsByName('local')[0], free: document.getElementsByName('free')[0]}

    }
    //This function runs when the page is loaded.
    function loadPage(){
        //getting the handle of elements
        addFormElemens();
        inputs.form.addEventListener('submit', handleSubmit);
        // Setting the handlers of checkes and button activations
        inputs.localSearch.onchange = function(){
            inputs.distance.disabled = !this.checked;
            inputs.here.disabled = !this.checked;
            inputs.zip.disabled = !this.checked;   
            if(!this.checked) {
                inputs.zipCode.disabled = true;
            }
            else if(this.checked && inputs.zip.checked){
                inputs.zipCode.disabled = false;
            }
        }
        inputs.zip.onchange = function(){
            inputs.zipCode.disabled = false;
        }
        inputs.here.onchange = function(){
            inputs.zipCode.disabled = true;
        }

        //Setting the disabled elements in the begining.
        inputs.distance.disabled = true;
        inputs.here.disabled = true;
        inputs.zip.disabled = true;
        inputs.zipCode.disabled = true;
        inputs.submit.disabled = true;

        getLocation();
    }
    
    //Fetching the location of the user
    function getLocation(){
            let xhttp;
            let location = 'http://ip-api.com/json/';
            let incoming;
            if(window.XMLHttpRequest){
                // modern browsers
                xhttp = new XMLHttpRequest();
            }else{
                xhttp = new ActiveXObject('Microsoft.XMLHTTP');
            }
            xhttp.onreadystatechange = () =>{
                if(xhttp.status == 200 && xhttp.readyState == 4){
                    incoming = JSON.parse(xhttp.responseText);
                    inputs.submit.disabled = false;
                    submitInfo.currentLoc = incoming.zip;                        
                }
            }
            xhttp.open("GET", location, false);
            xhttp.send();

    }

///HANDLING SUBMISSION OF THE FORM: CONTAINS handleSubmit(e), checkFields(), sendRequest().
    /* 
        Function gets the request form, prevents default submit behaviour of the form,  checks for the zip code format, 
        creates the request string and makes an XMLHttprequest to the backend code
     *  The fields are copied to the submitInfo from the input fields as follows: Keyword: category: Condition: shipping option: distance;
    */
    function handleSubmit(e){
        e.preventDefault();
        //checking the input fields for errors
        if (checkFields()){
            if(checkDistance()){
                let request = "";
                //preparing submitInfo
                submitInfo.keyword = inputs.keyword.value;
                // submitInfo.keyword = tempStr.replace(' ', '+');

                request += "keyword="+submitInfo.keyword;
                request += "&category="+inputs.category.value;
                request += "&new="+inputs.condition.new.checked;
                request += "&used="+inputs.condition.used.checked;
                request += "&unspecified="+inputs.condition.unspecified.checked;
                request += "&local="+inputs.pickup.local.checked;
                request += "&free="+inputs.pickup.free.checked;
                request += "&localSearch="+inputs.localSearch.checked;
                request += "&distance="+submitInfo.distance;
                request += "&zip="+submitInfo.zip;
                sendRequest(request, handleItemsResponse);
            }else{
                writeError('Distance is not valid')
            }
        } else {
            writeError("Zipcode is invalid")
        }
    }

    // Creates the error for zip 
    function writeError(error){
        //cleaning up the results
        while (results.firstChild) {
                    results.removeChild(results.firstChild);
        }
        let errorContainer = document.createElement('div');
            errorContainer.style.backgroundColor = '#efefef';
            errorContainer.style.border = '1px solid #ababab';
            errorContainer.style.margin = '0 auto';
            errorContainer.style.width = '800px';
            errorContainer.padding = '5%';
            errorContainer.innerHTML = error;
            results.appendChild(errorContainer);
            results.style.textAlign = 'center';
    }

    /* Checks if zipcode value is correct and updates the submitInfo.zip if necessary.
        Returns true if the check is successfull and false if its fails. */
    function checkFields(){
        if(inputs.localSearch.checked){
                if( inputs.zip.checked){
                let zipPattern = /^\d{5}$/;
                let zipCode = inputs.zipCode.value;
                // if (inputs.zipCode.value.match(zipPattern)){
                if (zipPattern.test(zipCode)){
                    submitInfo.zip = zipCode;
                    return true;
                }
                else{
                    return false;
                }
            }else {
                submitInfo.zip = submitInfo.currentLoc;
                return true
            }
        }else{
            submitInfo.zip = submitInfo.currentLoc;
            return true
        }
        
    }
    /* Checks if the distance is positive and an integer*/
    function checkDistance(){
        if(inputs.localSearch.checked){
            let distancePattern = /^\d+$/;
            let distance = inputs.distance.value;
            if (distancePattern.test(distance) && parseInt(distance) >= 0){
                submitInfo.distance = distance;
                return true;
            }
            else{
                return false;
            }
        }else {
            submitInfo.distance = inputs.distance.value;
            return true
        }
    }
    /* Takes a query string, issues a request to the server, which is the same file as it, than returns the results of the xmlhttprequest
        * Query string has to be in the format: name=value&name=value...
        * the response from the server is stored in a global variable named serverResponse.
    */
    function sendRequest(query, handler){
        let xhttp;
        if(window.XMLHttpRequest){//All other Browsers
           xhttp = new XMLHttpRequest();
        }else{//IE
            xhttp = new ActiveXObject('Microsoft.XMLHTTP');
        }
        xhttp.onreadystatechange = function(){ // Checking the response and returnign it 
            if(xhttp.readyState == 4 && xhttp.status==200){
                serverResponse = JSON.parse(xhttp.responseText);
                handler(serverResponse);
            }
        }
        xhttp.open('GET', './ProductSearch.php?server=true&' + query);
        xhttp.send(); 
    }


///FOR MULTI ITEMS
    function handleItemsResponse(response){

        if(response){
            if(response.findItemsAdvancedResponse[0].ack[0]==='Success'){
                 let searchResults = response.findItemsAdvancedResponse[0].searchResult;
                let count = searchResults[0]['@count'];
                if(count > 0){
                    let items = response.findItemsAdvancedResponse[0].searchResult[0].item;
                    //cleaning up the results
                    while (results.firstChild) {
                        results.removeChild(results.firstChild);
                    }
                    createItemsViews(items);
                }
                else{
                    writeError('No records have been found');
                }

            }else{
                writeError('No records have been found');
            }
               
          
        }else{
            console.log("Response is undefined");
        }
    }

    ///FOR MANY ITEMS: CREATING TABLE
    function createItemsViews(items){
            let table = document.createElement('tb');
            let headers = ['Index', 'Photo', 'Name','Price', 'Zipcode', 'Condition', 'Shipping Option']
          
            createTableHeader(headers, table);
            createTableDataForItems(items, table);
            results.style.textAlign = 'center';
    }

        ///CREATING THE TABLE USING THE RESPONSE 
        function createTableHeader(headers, table){
            let headerRow = document.createElement('tr');
            let i;
            let th;
            for (i=0; i < headers.length; i++){
                    th = document.createElement('th');
                    th.innerHTML = headers[i];
                    th.style.border = '1px solid gray';
                    th.style.padding = '0 2px';
                    headerRow.appendChild(th);
                }
                table.appendChild(headerRow);
                results.appendChild(table);
        }

        function createTableDataForItems(items, table){
            let item=[];//format: [index, photo(URL), name, price, zipcode, condition, shippingOption, itemID]
            let tr,td, img, p, link;
            let image;
            let shippingVal;
            let i, j;
            
            for(i=0; i<items.length;i++){
                    p = document.createElement('p');
                    p.innerHTML = i+1;
                item[0] = p;
                    image = items[i].galleryURL[0];
                    img = document.createElement('img');
                    img.src = image;
                    img.style.width = '70px';
                    img.style.height = 'auto';
                item[1] = img;
                    link = document.createElement('a');
                    link.classList.add('itemLink')
                    link.onclick = createItemViews(items[i].itemId[0]);
                    link.innerHTML = items[i].title[0];
                item[2] = link;
                    p = document.createElement('p');
                    p.innerHTML = '$' + items[i].sellingStatus[0].currentPrice[0].__value__;
                item[3]= p;
                    p = document.createElement('p');
                    if(items[i].postalCode){
                        p.innerHTML = items[i].postalCode[0];
                    }else{
                        p.innerHTML = "N/A";
                    }
                item[4] = p; 
                    p = document.createElement('p');
                    if(items[i].condition){
                        if(items[i].condition[0].conditionDisplayName){
                            p.innerHTML = items[i].condition[0].conditionDisplayName[0];

                        }else{
                            p.innerHTML = 'N/A';
                        }
                    }else{
                        p.innerHTML = 'N/A';
                    }
                item[5] = p;     
                    p = document.createElement('p');
                    s
                    if(shippingVal==0){
                        p.innerHTML = "Free Shipping";
                    }else{
                        p.innerHTML = '$' + shippingVal;
                    }
                item[6] = p;

                tr = document.createElement('tr');
                tr.style.border = '1px solid #000';

                for (j=0; j < item.length; j++){
                    td = document.createElement('td');
                    td.style.border = '1px solid #999';
                    td.style.textAlign = 'left';
                    td.style.verticalAlign = 'middle';
                    td.style.padding = '0 5px';
                    td.appendChild(item[j]);
                    tr.appendChild(td);
                }  
                table.appendChild(tr);
            }   
        }
    
///FOR SINGLE ITEM:
    //calls the api to get single item information and changes the view on the results to that.
    function createItemViews(itemId){
        //GetDetails
        return function(e) {
            let query = 'singleItem=true&itemID='+itemId;
            sendRequest(query, handleItemResponse);
            }
    }

    //is called when the server returns the item details
    function handleItemResponse(response){
        if(response){
                let item = response.Item;
                //cleaning up the results   
                while (results.firstChild) {
                    results.removeChild(results.firstChild);
                } 
            //creating the table Title
                let title = document.createElement('h1');
                title.innerHTML = "Item Details";
                results.appendChild(title);
            //table items
                createItemTable(item);


            //sellers Message Button
                let sellersMessage = document.createElement('div');
                let smTitle = document.createElement('p');
                let smButton = document.createElement('button');
                let smContainer = document.createElement('div');
                smContainer.innerHTML = '';
                sellersMessage.appendChild(smTitle);
                sellersMessage.appendChild(smButton);
                sellersMessage.appendChild(smContainer);
                sellersMessage.style.marginTop = '25px';
                smTitle.style.color = '#585858';
                smTitle.innerHTML = 'click to show sellers message';
                styleButton(smButton);

            //similar items 
                
                let similarItems = document.createElement('div');
                let siTitle = document.createElement('p');
                let siButton = document.createElement('button');   
                let siContainer = document.createElement('div');
                siContainer.innerHTML = '';
                similarItems.appendChild(siTitle);
                similarItems.appendChild(siButton);
                similarItems.appendChild(siContainer);
                similarItems.style.marginTop = '25px';
                siTitle.style.color = '#585858';
                siContainer.style.textAlign = 'center';

                siTitle.innerHTML = 'click to show similar items';
                styleButton(siButton);

            //adding button handlers
            smButton.onclick = handleSellersMessage(smContainer, smButton, siButton, siContainer, item.Description);
            siButton.onclick = handleSimilarItems(siContainer, siButton, smButton, smContainer, item.ItemID);

            //adding section to the results
            results.appendChild(sellersMessage);
            results.appendChild(similarItems);

        //Helper Functions
                function styleButton(btn){
                    btn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_down.png)';
                    btn.style.backgroundPosition = 'center';
                    btn.style.backgroundSize = 'contain';
                    btn.style.backgroundRepeat = 'no-repeat';
                    btn.style.backgroundColor = "transparent";
                    btn.style.width = '50px';
                    btn.style.height = '40px';
                    btn.style.border = 'none';
                }

        }else{
            console.log("Response is undefined");
        }

    }

    //Creates Item Details Table
    function createItemTable(item){
        let table, e;
        //Creating Main Data
            table = document.createElement('tb');
            //picture
            data = item.PictureURL[0];
            e = document.createElement('img');
            e.src = data;
            e.style.width = '300px';
            e.style.height = '300px';
            createRow(data, 'Photo', e, table);
            //title
            data = item.Title;
            e = document.createElement('p');
            e.innerHTML = data;
            createRow(data, 'Title', e, table);
            //Subtitle
            data = item.Subtitle;
            e = document.createElement('p');
            e.innerHTML = data;
            createRow(data, 'Sub Title', e, table);
            //Price
            data = item.CurrentPrice.Value;
            e = document.createElement('p');
            e.innerHTML = data + item.CurrentPrice.CurrencyID;
            createRow(data, 'Price', e, table);
            //Location
            data = item.Location;
            e = document.createElement('p');
            e.innerHTML = data + ', ' + item.PostalCode;
            createRow(data, 'Location', e, table);
            //Seller
            data = item.Seller.UserId;
            e = document.createElement('p');
            e.innerHTML = data;
            createRow(data, 'Seller', e, table);
            //Return Policy
            data = item.ReturnPolicy.ReturnsAccepted;
            e = document.createElement('p');
            e.innerHTML = data + ' within ' + item.ReturnPolicy.ReturnsWithin;
            createRow(data, 'Return Policy(US)', e, table);
        //Item Specifics
            if(item.ItemSpecifics){
                if(item.ItemSpecifics.NameValueList){
                    let i;
                    let th, tr, td;
                    data = item.ItemSpecifics.NameValueList;
                    for(i=0; i < data.length; i++){
                        tr = document.createElement('tr');
                        th = document.createElement('th');
                        td = document.createElement('td');
                        tr.appendChild(th);
                        tr.appendChild(td);
                        td.appendChild
                        th.innerHTML = data[i].Name;
                        td.innerHTML = data[i].Value[0];
                        styleTableElement(th);
                        styleTableElement(td);
                        table.appendChild(tr);
                    } 
                }else{
                    noData(table);
                }
            }else{
                noData(table);
            }
            results.appendChild(table);
        //Helper Functions
            function noData(table){
                let tr = document.createElement('tr');
                let th = document.createElement('th');
                let td = document.createElement('td');
                th.innerHTML = 'No Detail Infor From Seller';
                td.style.backgroundColor = '#efefef';
                tr.appendChild(th);
                tr.appendChild(td);
                styleTableElement(th);
                styleTableElement(td)
                table.appendChild(tr);
            }
            function styleTableElement(e){
                e.style.verticalAlign = 'middle';
                e.style.textAlign = 'left';
                e.style.border = '1px solid #bebebe';
                e.style.paddingLeft = '10px';
            }
            function createRow(data, header, e, table){
                let th, tr, td;
                if(data){
                    tr = document.createElement('tr');
                    th = document.createElement('th');
                    td = document.createElement('td');
                    th.innerHTML = header;
                    tr.appendChild(th);
                    tr.appendChild(td);
                    td.appendChild(e);
                    styleTableElement(th);
                    styleTableElement(td);
                    table.appendChild(tr);
                }
            }

    }
    
    //handles Sellers Message button press
    function handleSellersMessage(container, btn, otherBtn, otherContainer, content){
        return function(e){
            
            if(container.innerHTML == ''){
                btn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_up.png)';
                otherBtn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_down.png)';
                otherContainer.innerHTML = '';
                if(content){
                    let frame = createSellersMessage(content)
                    container.appendChild(frame) ;
                }else{
                    container.appendChild(itemDetailsError("Seller Does Not Have A Message."));
                    container.style.width = '900px';
                    container.style.margin = '0 auto';
                    container.style.border = '1px solid gray';
                }
                
            }else{
                container.innerHTML = '';
                btn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_down.png)';
            }
        }

    }

    //handles Similar Items button press
    function handleSimilarItems(container, btn, otherBtn, otherContainer, itemID){
        return function(e){
            if(container.innerHTML == ''){
                btn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_up.png)';
                otherBtn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_down.png)';
                otherContainer.innerHTML = '';
                
                let query = 'similarItems=true&itemID=' + itemID;
                sendRequest(query, getSimilarItems);

                function getSimilarItems(response){
                    if(response.getSimilarItemsResponse.ack==="Success" && response.getSimilarItemsResponse && response.getSimilarItemsResponse.itemRecommendations){
                            let items = response.getSimilarItemsResponse.itemRecommendations.item;
                            if(items.length > 0){
                                container.appendChild(createSimilarItems(items));
                            }else{
                                container.appendChild(itemDetailsError("No Similar Items Found."));
                            }  
                    }else{
                        container.appendChild(itemDetailsError("No Similar Items Found."));
                        container.style.width = '900px';
                        container.style.margin = '0 auto';
                        container.style.border = '1px solid gray';
                    }
                    
                }

            }else{
                container.innerHTML = '';
                btn.style.backgroundImage = 'url(http://csci571.com/hw/hw6/images/arrow_down.png)';
            }
        }
    }


    //Creates iframe with sellers message as content
    function createSellersMessage(content){
        let iframe = document.createElement('iframe');
        iframe.srcdoc = content;
        iframe.onload = function(){
            iframe.width = iframe.contentDocument.body.scrollWidth + 'px';
            iframe.height = iframe.contentDocument.body.scrollHeight + 'px';
        }
        iframe.scrolling = 'no';
        iframe.style.border = 'none';
        return iframe;
    }



    // Creates the similar items div with all items.
    function createSimilarItems(items){
        let i;
        let container = document.createElement('div');
        container.style.width = '900px';
        container.style.margin = '0 auto';
        container.style.border = '1px solid gray';
        container.style.overflowX = 'scroll';

        let table = document.createElement('table');
        container.appendChild(table);

        let tr = document.createElement('tr');
        table.appendChild(tr);
        let priceRow = document.createElement('tr');
        table.appendChild(priceRow);

        let td, img, a, p, itemContainer;
        for(i=0; i < items.length; i++){

            td = document.createElement('td');
            itemContainer = document.createElement('div');
            itemContainer.style.width = '150px';
            itemContainer.style.margin = '0 30px';
            td.appendChild(itemContainer);

            img = document.createElement('img');
            img.src = items[i].imageURL;
            img.style.width = '150px';
            img.style.height = '200px'
            img.style.display = 'block';
            itemContainer.appendChild(img);

            a = document.createElement('a');
            a.classList.add("itemLink");
            a.innerHTML = items[i].title;
            a.onclick = createItemViews(items[i].itemId);
            itemContainer.appendChild(a);
            tr.appendChild(td);

            td = document.createElement('td');
            p = document.createElement('p');
            p.innerHTML = '$' + items[i].buyItNowPrice.__value__;
            p.style.fontWeight = 'bold';
            p.style.margin = '10px'
            td.appendChild(p);
            td.style.textAlign = 'center';
            priceRow.appendChild(td);
            
        }

        return container;
    }

    //display error for itemDetails - returns the styled div to attach in a div
    function itemDetailsError(error){
        let container = document.createElement('div');
        let heading = document.createElement('h4');
        heading.innerHTML = error;
        container.appendChild(heading)
        container.style.textAlign = 'center';
        container.style.margin = '10px';
        container.style.border = '1px solid gray';
        return container;
    }
///Clearing Page
    function clearPage(){
        inputs.form.reset();
        while(results.firstChild){
            results.removeChild(results.firstChild);
        }
    }

    </script>


    </head>

<!-- START OF HTML -->

    <body onload = "loadPage()">
        <div class="container">
            <p class="form-header">Product Search</p>
            <hr class = "divider"/>


    <!-- This is where the form starts -->
            <div class="form-container">
                <form name="firstForm" class="main-form" >

                    <div class="row">
                        <label class="label"  for="keyword">Keyword</label>
                        <input type="text" size = 20 name="keyword" required/>  
                    </div>

                    <div class="row">
                        <label class="label"  for="category">Category</label>
                        <select name="category" id="category">
                            <option value="0"> All Categories</option>
                            <option value="550">Art</option>
                            <option value="2984">Baby</option>
                            <option value="267">Books</option>
                            <option value="11450">Clothing, Shoes &Accessories</option>
                            <option value="58058">Computers/Tablets & Networking</option>
                            <option value="26395">Health & Beauty</option>
                            <option value="11233">Music</option>
                            <option value="1249">Video Games & Consoles</option>
                        </select>
                    </div>

                    <div class="row">
                        <label class="label" for="condition">Condition</label>
                        <input class="checkbox" type="checkbox" name="new" value="yes"> New
                        <input class="checkbox" type="checkbox" name="used" value="yes"> Used
                        <input class="checkbox" type="checkbox" name="unspecified" value="yes"> Unspecified
                    </div>

                    <div class="row">
                        <label  class="label"  for="shipping"> Shipping Option</label>
                        <input class="checkbox" type="checkbox" name="local" value="yes"> Local Pickup
                        <input class="checkbox" type="checkbox" name="free" value="yes"> Free Shipping
                    </div>

                    <div class="local-search">
                        <input type="checkbox" name="nearby" value="yes"> 
                        <label>Enable Nearby Search</label>
                        <input type="text" size= 3 name = "distance" value="10" id="distance"><label>miles from</label>
                        <ul class="location-picker" >
                            <li class="list-item"><input type="radio" name="center" value = "here" checked>Here</li>
                            <li class="list-item">
                                <input type="radio" name="center" value="custom"> 
                                <input type="text" placeholder="zip code" size = 15 name="zip" required/>
                            </li>
                        </ul>
                    </div>

                    <div class="buttons">
                        <input type="submit" name="submit" value="Search" >
                        <input type="button" value="Clear" onclick = 'clearPage()'>
                    </div>
            </form>
            </div>
        </div>
        <div id="results"></div>
    </body>
    </html>
<!-- END OF HTML -->
<!-- END OF FRONT END CODE -->

<?php endif;?>