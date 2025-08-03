<script>
    var form_lock=false;
    function saveForm(ele,e,reset,success,error,final){
        e.preventDefault();
        if(form_lock){
            console.log('locked');
            return;
        }
        form_lock=true;
        const data=new FormData(ele);
        axios.post(ele.action,data)
        .then((res)=>{

            if(success){
                success(res.data,ele);
            }

            if(reset){
                ele.reset();
            }
        })
        .catch((err)=>{
            if(error){
                error(err,ele);
            }
        })
        .finally(()=>{
            form_lock=false;
            if(final){
                final();
            }
        });
    }

    function yes(message=""){
        if(message==""){
            message="Do you want to continue?";
        }

        return (prompt(message)??"").toLowerCase() =="yes";
    }

    function toNepaliDate($){if(!$)return"";{let t=parseInt($/1e4),_=$%1e4,e=parseInt(_/100);return""+t+"-"+(e<10?"0"+e:e)+"-"+((_%=100)<10?"0"+_:_)}}
    function renderBalance(r){return 0===r?"--":r<0?(-1*r).toString()+" Cr.":r.toString()+" Dr."}

    function searchTable(input_id,table_id,eacapes=[]) {
        document.getElementById(input_id).addEventListener('input', function () {
            const searchText = this.value.toLowerCase();
            const table = document.getElementById(table_id);
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    if(eacapes.includes(j)){
                        continue;
                    }
                    const cellText = cells[j].innerText.toLowerCase();
                    if (cellText.includes(searchText)) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

</script>
