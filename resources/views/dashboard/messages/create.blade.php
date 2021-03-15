<x-dashboard>

<main class="content">
	<div class="container-fluid p-0">
        @if($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                <div class="alert-message">
                    {{ $error }}
                </div>
                </div>
            @endforeach
        @endif
         @if(session()->has('success'))
        	<div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            <div class="alert-message">
              {{ session()->get('success') }}
            </div>
          </div>
          @endif
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Create New Message and Campaign
                          <a href="{{ route('message.index') }}" class="btn btn-outline-dark float-right" >
                          <span class="align-middle" data-feather="chevron-left" ></span>back
                          </a>
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('message.store') }}" enctype="multipart/form-data"> @csrf
                            <div class="mb-3">
                                <label class="form-label">Write message<span class="text-danger">*</span></label>
                                <textarea cols="5" id="msg" oncontextmenu="return false" onkeyup="checkTextArea();" class="form-control" name="message">{{ old('messsage')}} </textarea>
                                <small id="info">0 character(s) 1 SMS message(s)</small>
                                <input type="hidden" id="na" name='no_of_sms' value="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select any one <span class="text-danger">*</span></label>
                                </br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="single_message" value="single">
                                    <label class="form-check-label" for="inlineRadio1">single message</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="bulk_message" value="bulk">
                                    <label class="form-check-label" for="inlineRadio2">bulk messages</label>
                                </div>
                            </div>
                            <div class="mb-3" id="someThingHere">
                               
                            </div>
                            @if(Auth::user()->getUserSmsApi->type != 'code')
                            <div class="mb-3">
                                <label class="form-label">Masking <span class="text-danger">*</span></label>
                                <select class="select2 form-control" name="masking_id" data-placeholder="Select multiple masking">
                                    @foreach ($maskings as $masking )
                                        <option {{ (old('masking') == $masking->id)? 'selected': null }} value="{{ $masking->id }}">{{ $masking->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="late_shedule" class="custom-control-input" id="lateShedule">
                                    <label class="custom-control-label" for="lateShedule">Select Shedule For Late Sending</label>
                                </div>
                            </div>

                            <div class="mb-3" id="latemessagedatetime">
                               
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>

$(document).ready(function(){
    $('#single_message').change(function(){
        $('#someThingHere').empty();
        if ($(this).is(':checked')) {
            var $_html = 
            `<label class="form-label">Phone Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="phone_number" placeholder="phone number" value="{{ old('phone_number') }}">`;
            $('#someThingHere').append($_html);
        }
    });

    $('#bulk_message').change(function(){
        $('#someThingHere').empty();
        if ($(this).is(':checked')) {
            var $_html2 = 
            `<div class="row">
                <div class="col-md-6">
                    <label class="form-label">Campaign Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="campaign" placeholder="Enter the Campaign Name" value="{{ old('campaign') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Import excel<span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input" id="customFile" accept=".xls, .xlsx">
                        <label class="custom-file-label" for="customFile">Import your excel</label>
                    </div>
                </div>

            </div>`;
    
           $('#someThingHere').append($_html2);
 
        }
    });

    $('#lateShedule').click(function(){
        if($(this).prop('checked')){
            var $_htmldatetime =  
            `<label for="example-datetime-local-input" >Date and time <span class="text-danger">*</span></label>
                <input class="form-control" type="datetime-local" value="" name="sheduledatetime">
            `;
            $('#latemessagedatetime').append($_htmldatetime);
        }else{
            $('#latemessagedatetime').empty();
        }
    });
 
});

    function getTextWithCorrectNewLines(){
        var msg = $('#msg').val();
        msg = msg.replace(/\r\n/g," ").replace(/\n\r/g," ").replace(/\r/g," ").replace(/\n/g," ");
        return msg;
    }

    var nonuniarray1 = new Array(
            0x0040,0x00A3,0x0024,0x00A5,0x00E8,0x00E9,0x00F9,0x00EC,0x00F2,0x00E7,0x000A,0x00D8,0x00F8,0x000D,0x00C5,0x00E5,
            0x0394,0x005F,0x03A6,0x0393,0x039B,0x03A9,0x03A0,0x03A8,0x03A3,0x0398,0x039E,0x00A0,0x00C6,0x00E6,0x00DF,0x00C9,
            0x0020,0x0021,0x0022,0x0023,0x00A4,0x0025,0x0026,0x0027,0x0028,0x0029,0x002A,0x002B,0x002C,0x002D,0x002E,0x002F,
            0x0030,0x0031,0x0032,0x0033,0x0034,0x0035,0x0036,0x0037,0x0038,0x0039,0x003A,0x003B,0x003C,0x003D,0x003E,0x003F,
            0x00A1,0x0041,0x0042,0x0043,0x0044,0x0045,0x0046,0x0047,
            0x0048,0x0049,0x004A,0x004B,0x004C,0x004D,0x004E,0x004F,
            0x0050,0x0051,0x0052,0x0053,0x0054,0x0055,0x0056,0x0057,
            0x0058,0x0059,0x005A,0x00C4,0x00D6,0x00D1,0x00DC,0x00A7,
            0x00BF,0x0061,0x0062,0x0063,0x0064,0x0065,0x0066,0x0067,
            0x0068,0x0069,0x006A,0x006B,0x006C,0x006D,0x006E,0x006F,
            0x0070,0x0071,0x0072,0x0073,0x0074,0x0075,0x0076,0x0077,
            0x0078,0x0079,0x007A,0x00E4,0x00F6,0x00F1,0x00FC,0x00E0);
    var nonuniarray2 = new Array(0x000C,0x005E,0x007B,0x007D,0x005C,0x005B,0x007E,0x005D,0x007C,0x20AC);
    
    function checkTextArea(){
        var isUnicode = false;
        var code = 0;
        var smsCounter = 0;
        var position = 0;
        var doubleChar = 0;
        var text = getTextWithCorrectNewLines();

        if(!Array.indexOf){
            Array.prototype.indexOf = function(obj){
            var ret = -1;
            for(var i=0; i<this.length; i++){
                if(this[i]==obj){
                    ret = i;
                }
            }
            return ret;
            }
        }

        while(!isUnicode && (text.length > position)){
            code = text.charCodeAt(position);
            if(nonuniarray1.indexOf(code)==-1 && nonuniarray2.indexOf(code)==-1){
            isUnicode = true;
            break;
            } else { if(nonuniarray2.indexOf(code)!=-1) doubleChar++; }
                
            position++;
        }

        if (isUnicode){
            if (text.length <= 70) {
                smsCounter = 1;
            }else {
                smsCounter = Math.ceil(text.length / 67);
            }
                document.getElementById('info').innerHTML = "" + text.length + " unicode character(s), " + smsCounter + " SMS message(s)";
                document.getElementById('na').value = smsCounter;
        }else{
            if ((text.length + doubleChar) <= 160){
                smsCounter = 1;
            }else{
                smsCounter = Math.ceil((text.length + doubleChar) / 153);
            }
        
            document.getElementById('info').innerHTML = "" + (text.length+doubleChar) + " character(s), " + smsCounter + " SMS message(s)";
            document.getElementById('na').value = smsCounter;
        }

        if(isUnicode){
            document.getElementById('msg').setAttribute("maxlength", 335);
        }else{
            document.getElementById('msg').setAttribute("maxlength", 765);
        }

        return getTextWithCorrectNewLines();

   }
</script>
</x-dashboard>
