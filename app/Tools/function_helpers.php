<?php
/**
 * Fungsi untuk membantu berbagai proses
 * User: toni
 * Date: 25/05/15
 * Time: 14:05
 */
if(!function_exists('load_input_value'))
{
    /**
     * Kita gunakan ini di bagian untuk melakukan setting terhadap value sebuah inputan!
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $field
     */
    function load_input_value($model,
                              $field)
    {
        $value = old($field);
        // check apakah ini adalah editing yang tidak langsung dimasukkan nilainya?
        if($value!==null)
        {
            return $value;
        }
        // bila tidak (awal mengedit) maka nilai field ada
        // kembalikan saja nilainya!
        return $model===null? null : $model->{$field};
    }
}
if(!function_exists("load_select"))
{
    /**
     * Load sebuah list!
     * Sebagai contoh:
     * {!! load_select('updetan_atas', $kandidat, $kandidatTerpilih, ['class'=>"form-control"]) !!}
     * @param string $nama nama si select
     * @param array $kandidat kendidat items yang akan di generate
     * @param array|int $terpilih data yang terpilih
     * @param array $options opsi tambahan untuk select
     * @param array $addTop penambahan ke bagian paling atas untuk penjelasan. Tapi bila nilai index = 0 maka item ini tidak memiliki value
     * @return string kembalian hasil genearte
     */
    function load_select($nama, $kandidat, $terpilih, array $options=[], $addTop=['Pilih Pilihan'], $onlyOption=false)
    {
        $gotSelected = false;
        // generate attributes
        $attrs = "";
        if(count($options)>0)
        {
            foreach ($options as $attr => $value) {
                $attrs .= " $attr=\"$value\"";
            }
        }

        // jadikan array untuk terpilih bila bukan array
        if(!is_array($terpilih))
        {
            $terpilih = [$terpilih];
        }
        $terpilih = array_flip($terpilih);

        // generate option
        $opt = "";
        foreach ($kandidat as $key=>$value) {
            if(array_key_exists($key, $terpilih))
            {
                $opt.="<option selected value=\"{$key}\">{$value}</option>";
                $gotSelected = true;
            }
            else
            {
                $opt.="<option value=\"{$key}\">{$value}</option>";
            }
        }

        // apakah ada tambahan di bagian atas?
        if($addTop!=null)
        {
            $top = "";
            if(is_array($addTop))
            {
                $top =$addTop[0];
            }
            else
            {
                $top = $addTop;
            }
//            foreach($addTop as $key=>$value)
//            {
////                if($key!==0) // jangan berikan nilai!
////                {
////                    $opt="<option value=\"{$key}\">{$value}</option>".$opt;
////                }
////                else
////                {
            if($gotSelected)
            {
                $opt="<option value=\"\" disabled>{$top}</option>".$opt;
            }
            else
            {
                $opt="<option value=\"\" disabled selected>{$top}</option>".$opt;
                $gotSelected = true;
            }
////                }
//            }
        }

        // lakukan pengembalian!
        return $onlyOption? $opt: "<select id=\"$nama\" name=\"$nama\"$attrs>$opt</select>";
    }
}

if(!function_exists('load_select_model'))
{
    /**
     * Load select/drop down tapi disini mengambil model langsung untuk mendapatkan kandidat yang terpilih, berbeda
     * dengan load_select
     * @param string $nama nama dari field
     * @param array $kandidat kandidat items yang akan digenerate
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param array $options opsi tambahan untuk select
     * @param array $addTop tambahan untuk menambah di bagian atas dropdown, bila di set dengan 0 maka nilai ini tidak memiliki nilai
     * @param bool $onlyOption apakah hanya akan mengembalikan options saja tanpa ditutup tag <select></select>?
     * @return string kembalian hasil gnerate
     */
    function load_select_model($nama, $kandidat, $data, array $options=[], $addTop=['Pilih Pilihan'], $onlyOption=false)
    {
        $terpilih = load_input_value($data, $nama);
        $terpilih = $terpilih===null? "":$terpilih;
        return load_select($nama, $kandidat, $terpilih, $options, $addTop, $onlyOption);
    }
}
if(!function_exists('load_check_box'))
{
    function load_check_box($nama, $data, $dvalue, $label, array $options = [])
    {
        $value = [];
        $value[] = "name=\"$nama\" id=\"$nama\" type=\"checkbox\" value=\"$dvalue\"";
        $attrs = "";
        if(count($options)>0)
        {
            foreach ($options as $attr => $value) {
                $attrs = " $attr=\"$value\"";
            }
        }
        $value[] = $attrs;
        $terpilih = load_input_value($data, $nama);
        if($terpilih!==null && $terpilih==$dvalue)
        {
            $value[] = 'checked';
        }
        return "<input ". implode(" ", $value) . ">$label";
    }
}
if(!function_exists('load_radio_button'))
{
    function load_radio_button($nama, $data, array $valueLabel, array $options=[])
    {
        $os = [];
        $attrs = "";
        if(count($options)>0)
        {
            foreach ($options as $attr => $value) {
                $attrs = " $attr=\"$value\"";
            }
        }
        $ch = load_input_value($data, $nama);
        foreach($valueLabel as $value=>$label)
        {
            $checked = ($ch==$value? " checked":"");
            $os[] = "<label class=\"radio-inline\">".
                "<input name=\"$nama\" id=\"$nama\" type=\"radio\" value=\"$value\"{$attrs}{$checked}> $label".
                "</label>";
        }
        return implode("",$os);
    }
}
if(!function_exists('convert_date_to'))
{
    /**
     * Lakukan konversi tanggal/date dari suatu format yang di spesifikasikan oleh $formatIn dan target format keluaran
     * adalah dispesifikasikan oleh $formatTarget.
     * @param string $formatIn format masuk dari value
     * @param string $value nilai
     * @param string $formatTarget
     * @return string hasil convert!
     */
    function convert_date_to($formatIn, $value, $formatTarget = 'Y-m-d')
    {
        if(strlen($value)<=0) return null; // kembalikan null karena nilai $value = ""
        $d = date_parse_from_format($formatIn, $value);
        if($d['year']==0 || $d['month']==0 || $d['day']==0) return null; // ini invalid!
        return date($formatTarget, mktime(0,0,0,$d['month'], $d['day'], $d['year']));
    }
}