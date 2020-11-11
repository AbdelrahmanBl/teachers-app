<div class="content2" style="display: none;" id="preview" >
                    <h3 class="E1" >الموافقة على الطالب أو عدم الموافقة عليه</h3>
                    <p class="E2" >هنا يتم عرض الطلاب الذين قاموا بإنشاء حساب وعند الضغط على زر الموافقة سوف يكون ذلك الطالب مستخدمًا بالموقع ويكون قادرًا على التفاعل مع ما ينشره المُعلم من امتحانات</p>
                    <div class="wrapper" >
                        <h1 class="E3" >امتحان جديد</h1>
                          <div class="E4" >
                              <input class="examMark" id="examMarkAdd" type="text" placeholder="درجة السؤال" > 
                          </div>
                          <div class="choice" id="choice" >
                            <div class="div_mcq" >
                                 <input  type="radio" class="mcq" id="mcqAdd" name="cons"  >
                                <span class="E5" >اختيار من مُتعدد</span>
                            </div>
                           
                            </div>
                            
                        <div class="WritingQ" id="WritingQ" >
                            <div class="writing_div" id="writing_div" >
                        <div class="E8" id="toolbar-containerAdd" >
                            <select class="ql-color">
                                  <option selected></option>
                                  <option value="red"></option>
                                  <option value="orange"></option>
                                  <option value="yellow"></option>
                                  <option value="green"></option>
                                  <option value="blue"></option>
                                  <option value="purple"></option>
                            </select>
                            <span  class="ql-formats">
                                <select  class="ql-size">
                                   <option value="10px">10px</option>
                                  <option value="12px">12px</option>
                                  <option value="14px">14px</option>
                                  <option value="16px">16px</option>
                                  <option value="18px">18px</option>
                                  <option value="20px">20px</option>
                                  <option value="22px">22px</option>
                                  <option value="24px">24px</option>
                                  <option value="26px">26px</option>
                                  <option value="28px">28px</option>
                                  <option value="30px">30px</option>
                                  <option value="32px">32px</option>
                                  <option value="34px">34px</option>
                                  <option value="36px">36px</option>
                                  <option value="38px">38px</option>
                                  <option value="40px">40px</option>
                                </select>
                              </span>
                           <select class="ql-font">
                            <option selected disabled>font</option>
                            <option value="amiri">Amiri</option>
                            <option value="cairo">Cairo</option>
                          </select>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-direction" value="rtl" type="button"></button>
                                <select class="ql-align">
                                    <option selected="selected"></option>
                                    <option value="center"></option>
                                    <option value="right"></option>
                                    <option value="justify"></option>
                                </select>
                            </span>
                        </div>
                            <div id="editorAdd" ></div>

                         </div>
                           
                            <div class="nocompleteAdd" >
                                <div class="mcq_divAdd" id="mcq_divAdd" >
                                <div class="E7" >
                                    <button class="addAnsAdd" id="addAnsAdd" >+</button>
                                    <input class="qNAdd" id="qNAdd" type="text" @if(session('Edit')[8] == 2) style="text-align: left;padding-left: 5px;direction: ltr;" @endif placeholder="اكتب السؤال" >
                                    <input class="aNAdd" id="aNAdd" type="text" @if(session('Edit')[8] == 2) style="text-align: left;padding-left: 5px;direction: ltr;" @endif  placeholder=" الإجابة الصحيحة" >
                                </div>
                                <div class="answersAdd" id="answersAdd" @if(session('Edit')[8] == 2) direction: ltr; @endif>
                                </div>
                                </div>
                                
                        </div>
                            <div class="wea" id="wea" style="height: auto; width: 100%; padding: 8px; text-align: right; display: none; margin-top: 15px">
                                <button class="btnForClose"  >إغلاق</button>
                                <span class="error" id="degreeErr2" style="color: red"></span>
                                <button class="btnForWriting" id="MCQBtn" onclick="addMCQ()" >حفظ الآن</button>

                            </div>
                        </div>
                    </div>
                </div>
<script>
    function addMCQ(){
        $("#MCQBtn").attr("disabled", true);
        $("#MCQBtn").html("... جاري الاضافة");
     var arr = [];
        if($("#mcqAdd").is(":checked")){
            var degree = $('#examMarkAdd').val();
            var Question = $('#qNAdd').val();
            var TR = $('#aNAdd').val();
            var Main = $("#editorAdd").find(".ql-editor").html();
            if( Main == "<p><br></p>" || Main == '<p class="ql-direction-rtl ql-align-right"><br></p>' )
                Main = "";
            for(var i = 1 ; i <= counter ; i++){
            if( typeof( $("#ansAdd"+i).val() ) === 'undefined' || $("#ansAdd"+i).val() == ""  )
            continue;
            arr.push($("#ansAdd"+i).val())   
            }
            console.log(arr);
            if(arr.length == 0 ){
              $('#degreeErr2').html('برجاء وضع الاجابات');
                $('#degreeErr2').hide();
                $('#degreeErr2').show('slow');
                $("#MCQBtn").attr("disabled", false);
                $("#MCQBtn").html("حفظ الآن");
                return 0;
            }
            if(!arr.includes(TR)){
                $('#degreeErr2').html('لا توجد اجابة مشابهة للاجابة الصحيحة');
                $('#degreeErr2').hide();
                $('#degreeErr2').show('slow');
                arr = [];
                $("#MCQBtn").attr("disabled", false);
                $("#MCQBtn").html("حفظ الآن");
                return 0;
            }
            $.ajax({
            type: "POST",
            dataType: "json",
            url: "/addMCQ", 
            data: {_token:"{{csrf_token()}}", degree : degree , Question : Question , TR : TR , Main : Main , Responds : arr }
        }).done( function(data){
          //alert(data[0])
            if(data[0] == "success" ){      
                location.href = 'bank';
            }
            else{
                $('#degreeErr2').html('برجاء مراجعة البيانات');
                $('#degreeErr2').hide();
                $('#degreeErr2').show('slow');
                $("#MCQBtn").attr("disabled", false);
                $("#MCQBtn").html("حفظ الآن");
            }
        });
      }

    }

    </script>                