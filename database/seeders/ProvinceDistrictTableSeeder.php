<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceDistrictTableSeeder extends Seeder
{
    private $data=[
        'Province 1'=>['Bhojpur','Dhankuta', 'Ilam', 'Jhapa', 'Khotang', 'Morang','Okhaldhunga', 'Panchthar', 'Sankhuwasabha', 'Solukhumbu','Sunsari', 'Taplejung', 'Terhathum', 'Udayapur'],
        'Province 2'=>['Bara', 'Dhanusa', 'Mahottari', 'Parsa','Rautahat', 'Saptari', 'Sarlahi', 'Siraha'],
        'Bagmati Pradesh'=>['Bhaktapur','Chitwan','Dhading','Dolakha','Kathmandu','Kavrepalanchok','Lalitpur','Makawanpur','Nuwakot','Ramechhap','Rasuwa','Sindhuli','Sindhupalchok'],
        'Gandaki Pradesh'=>['Baglung','Gorkha','Kaski','Lamjung','Manang','Mustang','Myagdi','Nawalpur','Parbat','Syangja','Tanahu'],
        'Lumbini Pradesh'=>['Arghakhanchi','Banke','Bardiya','Dang','Gulmi','Kapilvastu','Parasi','Palpa','Pyuthan','Rolpa','Rukum','Rupandehi'],
        'Karnali Pradesh'=>['Dailekh','Dolpa','Humla','Jajarkot','Jumla','Kalikot','Mugu','Rukum','Salyan','Surkhet'],
        'Sudurpashchim Pradesh'=>['Achham','Baitadi','Bajhang','Bajura','Dadeldhura','Darchula','Doti','Kailali','Kanchanpur']
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->data as $province => $districts){
            $province_id = DB::table('provinces')->insertGetId([
                'province_name'=> $province
            ]);

            foreach($districts as $district){
                DB::table('districts')->insert([
                'district_name' => $district,
                'province_id'=>$province_id
                ]);
            }
        }
         
    }
}
