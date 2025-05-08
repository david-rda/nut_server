<div>
    <p>თქვენი განაცხადი, ზედნადების ნომრით: <b>{{ \App\Models\Statement::where("id", $statement_id)->first()->overhead_number }}</b> დახარვეზდა.</p>
    <p>გთხოვთ დროულად მოახდინოთ ინფორმაციის შესწორება.</p>

    <p>განაცხადის სანახავად გადადით <a href="https://nuts.rda.gov.ge/statement/read/{{ $statement_id }}">ბმულზე</a></p>

    <br><br>
    <img src="https://survey.rda.gov.ge/rda.png" width="150px">
    <p style="color:green;font-weight:bold">სოფლის განვითარების სააგენტო</p>
    <p style="color:gray;">საქართველო, ქ. თბილისი 0159, ახმეტელის 10ა</p>
    <p style="color:gray;">ტელ: 1501</p>
    <p style="color:gray;">ელ. ფოსტა: <a href="mailto:info@rda.gov.ge">info@rda.gov.ge</a></p>
    <p style="color:gray;">ვებგვერდი: <a href="www.rda.gov.ge" target="_blank">www.rda.gov.ge</a></p>
</div>