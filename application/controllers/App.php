<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('No_urut');
    }

    public function bypass_ujian_pmb($username)
    {
        // Load database kedua
        $web_cat = $this->load->database('web_cat', TRUE);
        $cek_user = $web_cat->query("SELECT * FROM user WHERE username='$username' ");
        if ($cek_user->num_rows() > 0) {
            foreach ($cek_user->result() as $row) {
                
                $sess_data['id_user'] = $row->user_id;
                $sess_data['nama'] = $row->nama_lengkap;
                $sess_data['username'] = $row->username;
                $sess_data['level'] = $row->akses;
                $this->session->set_userdata($sess_data);
            }
            

            redirect('ujian_pmb/app/index');
        }
    }

	public function index()
	{
		$data = array(
			'konten' => 'home',
		);
		$this->load->view('v_index', $data);
	}

	public function download_brosur()
	{
		$data = array(
			'konten' => 'download_brosur',
		);
		$this->load->view('v_index', $data);
	}
	
	public function ubah_daftar($no_pendaftaran)
	{
		$data = array(
			'konten' => 'ubah_daftar',
			'data' => $this->db->query("SELECT * FROM pendaftaran WHERE no_pendaftaran='$no_pendaftaran'"),
		);
		$this->load->view('v_index', $data);
	}

	public function cetak_kartu($no_pendaftaran)
	{
	    $metode = $this->db->query("SELECT metode_pembayaran from pendaftaran WHERE no_pendaftaran='$no_pendaftaran'")->row();
		$cek = $this->db->query("SELECT b.status from pendaftaran as p, bukti_pembayaran as b WHERE p.no_pendaftaran=b.no_pendaftaran and b.no_pendaftaran='$no_pendaftaran' and b.status='1' ");
		if ($metode->metode_pembayaran == 'Tranfer Bank') {
		    
    		if ($cek->num_rows() == 1) {
    			$data = array(
    				'no_pendaftaran' => $no_pendaftaran,
    			);
    			$this->load->view('cetak_kartu', $data);
    		} else {
    			?>
    			<script type="text/javascript">
    				alert('anda tidak bisa cetak kartu ujian, karna belum melakukan pembayaran pendaftaran');
    				window.location = '<?php echo base_url() ?>app'
    			</script>
    			<?php
    		}
		} else {
		    $data = array(
    				'no_pendaftaran' => $no_pendaftaran,
    			);
    			$this->load->view('cetak_kartu', $data);
		}
		
	}

	function ubah_status($id)
	{
		$this->db->where('id_pembayaran', $id);
		$this->db->update('bukti_pembayaran', array('status' => '1'));
		redirect('bukti_pembayaran','refresh');
	}

	public function cetak_pendaftaran($no_pendaftaran)
	{
		$data = array(
				'data' => $this->db->query("SELECT * FROM pendaftaran where no_pendaftaran='$no_pendaftaran' "),
			);
		$this->load->view('cetak_pendaftaran', $data);
	}

	public function upload_bukti()
	{
		$data = array(
			'konten' => 'formbukti',
		);
		$this->load->view('v_index', $data);
	}

	public function formdaftar()
	{
		$data = array(
			'konten' => 'formdaftar',
		);
		$this->load->view('v_index', $data);
	}



	public function simpan_formdaftar()
	{
		$nama = $this->input->post('nama');
		$tempat = $this->input->post('tempat');
		$tgl = $this->input->post('tgl');
		$alamat = $this->input->post('alamat');
		$rt = $this->input->post('rt');
		$rw = $this->input->post('rw');
		$no_rumah = $this->input->post('no_rumah');
		$kode_pos = $this->input->post('kode_pos');
		$no_telp = $this->input->post('no_telp');
		$tempat_tinggal = $this->input->post('tempat_tinggal');
		$status_kawin = $this->input->post('status_kawin');
		$jenis_pekerjaan = $this->input->post('jenis_pekerjaan');
		$kewarganegaraan = $this->input->post('kewarganegaraan');
		$agama = $this->input->post('agama');
		$hobi = $this->input->post('hobi');
		$slta = $this->input->post('slta');
		$jurusan = $this->input->post('jurusan');
		$no_sttb = $this->input->post('no_sttb');
		$tgl_sttb = $this->input->post('tgl_sttb');
		$tempat_sttb = $this->input->post('tempat_sttb');
		$pilihan_studi = $this->input->post('pilihan_studi');
		$password = $this->input->post('password');
		$kode_pendaftaran = $this->No_urut->buat_kode_pendaftaran();
		$tahun_lulus = $this->input->post('tahun_lulus');
		$metode_pembayaran = $this->input->post('metode_pembayaran');

		date_default_timezone_set('Asia/Jakarta');
			$nmfile = "pmb_".time();
            $config['upload_path'] = './files/foto';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = '20000';
            $config['file_name'] = $nmfile;
            // load library upload
            $this->load->library('upload', $config);
            // upload gambar 1
            $this->upload->do_upload('foto');
            $result1 = $this->upload->data();
            $result = array('gambar'=>$result1);
            $dfile = $result['gambar']['file_name'];

		$data = array(
			'no_pendaftaran' => $kode_pendaftaran,
			'nama_lengkap' => $nama,
			'tempat' => $tempat,
			'tgl_lahir' => $tgl,
			'alamat' => $alamat,
			'rt' => $rt,
			'rw' => $rw,
			'no_rumah' => $no_rumah,
			'kode_pos' => $kode_pos,
			'no_telp' => $no_telp,
			'tempat_tinggal' => $tempat_tinggal,
			'status_kawin' => $status_kawin,
			'jenis_pekerjaan' => $jenis_pekerjaan,
			'kewarganegaraan' => $kewarganegaraan,
			'agama' => $agama,
			'hobi' => $hobi,
			'slta' => $slta,
			'jurusan' => $jurusan,
			'no_sttb' => $no_sttb,
			'tgl_sttb' => $tgl_sttb,
			'tempat_sttb' => $tempat_sttb,
			'pilihan_studi' => $pilihan_studi,
			'tahun_lulus' => $tahun_lulus,
			'metode_pembayaran' => $metode_pembayaran,
			'foto' => $dfile,
			'tgl_buat' => date('Y-m-d'),
            'jam_buat' => date('H:i:s'),
		);
		$cekdb = $this->db->query("SELECT * FROM pendaftaran WHERE nama_lengkap='$nama' ");
		if($cekdb->num_rows() >= 1){
		    ?>
		    <script type="text/javascript">
                alert('Data sudah ada, mohon inputkan kembali !');
                window.location="<?php echo base_url() ?>app/formdaftar";
            </script>
		    <?php
		} else {
    		$simpan = $this->db->insert('pendaftaran', $data);
    		if ($simpan) {
    			$this->db->insert('users',array('username'=>$kode_pendaftaran,'password'=>md5($password),'level'=>'mahasiswa'));

                //insert user web cat
                // Load database kedua
                $web_cat = $this->load->database('web_cat', TRUE);
                $web_cat->insert('user',array(
                    'username'=>$kode_pendaftaran,
                    'password'=>md5($password),'akses'=>'siswa',
                    'nama_lengkap'=>$nama,
                    'no_hp'=>$no_telp,
                ));

    			$this->session->set_flashdata('pesan','Selamat anda telah berhasil mendaftar !');
    			$this->session->set_flashdata('username',$kode_pendaftaran);
    			$this->session->set_flashdata('password',$password);
    			redirect('app/selesai_daftar');
    		} else {
    			redirect('app');
    		}
		}
		
	}
	
	public function aksiubah_formdaftar($no_pendaftaran)
    {

        if ($_FILES['foto']['name'] == '' ) {
            $nama = $this->input->post('nama');
            $tempat = $this->input->post('tempat');
            $tgl = $this->input->post('tgl');
            $alamat = $this->input->post('alamat');
            $rt = $this->input->post('rt');
            $rw = $this->input->post('rw');
            $no_rumah = $this->input->post('no_rumah');
            $kode_pos = $this->input->post('kode_pos');
            $no_telp = $this->input->post('no_telp');
            $tempat_tinggal = $this->input->post('tempat_tinggal');
            $status_kawin = $this->input->post('status_kawin');
            $jenis_pekerjaan = $this->input->post('jenis_pekerjaan');
            $kewarganegaraan = $this->input->post('kewarganegaraan');
            $agama = $this->input->post('agama');
            $hobi = $this->input->post('hobi');
            $slta = $this->input->post('slta');
            $jurusan = $this->input->post('jurusan');
            $no_sttb = $this->input->post('no_sttb');
            $tgl_sttb = $this->input->post('tgl_sttb');
            $tempat_sttb = $this->input->post('tempat_sttb');
            $pilihan_studi = $this->input->post('pilihan_studi');
            $tahun_lulus = $this->input->post('tahun_lulus');
            $metode_pembayaran = $this->input->post('metode_pembayaran');

            $data = array(
                'nama_lengkap' => $nama,
                'tempat' => $tempat,
                'tgl_lahir' => $tgl,
                'alamat' => $alamat,
                'rt' => $rt,
                'rw' => $rw,
                'no_rumah' => $no_rumah,
                'kode_pos' => $kode_pos,
                'no_telp' => $no_telp,
                'tempat_tinggal' => $tempat_tinggal,
                'status_kawin' => $status_kawin,
                'jenis_pekerjaan' => $jenis_pekerjaan,
                'kewarganegaraan' => $kewarganegaraan,
                'agama' => $agama,
                'hobi' => $hobi,
                'slta' => $slta,
                'jurusan' => $jurusan,
                'no_sttb' => $no_sttb,
                'tgl_sttb' => $tgl_sttb,
                'tempat_sttb' => $tempat_sttb,
                'pilihan_studi' => $pilihan_studi,
                'tahun_lulus' => $tahun_lulus,
                'metode_pembayaran' => $metode_pembayaran,
                'tgl_buat' => date('Y-m-d'),
                'jam_buat' => date('H:i:s'),
            );
            $this->db->where('no_pendaftaran', $no_pendaftaran);
            $this->db->update('pendaftaran', $data);
            ?>
            <script type="text/javascript">
                alert('Data berhasil di ubah');
                window.location="<?php echo base_url() ?>app";
            </script>
            <?php
        } else {
            $nama = $this->input->post('nama');
            $tempat = $this->input->post('tempat');
            $tgl = $this->input->post('tgl');
            $alamat = $this->input->post('alamat');
            $rt = $this->input->post('rt');
            $rw = $this->input->post('rw');
            $no_rumah = $this->input->post('no_rumah');
            $kode_pos = $this->input->post('kode_pos');
            $no_telp = $this->input->post('no_telp');
            $tempat_tinggal = $this->input->post('tempat_tinggal');
            $status_kawin = $this->input->post('status_kawin');
            $jenis_pekerjaan = $this->input->post('jenis_pekerjaan');
            $kewarganegaraan = $this->input->post('kewarganegaraan');
            $agama = $this->input->post('agama');
            $hobi = $this->input->post('hobi');
            $slta = $this->input->post('slta');
            $jurusan = $this->input->post('jurusan');
            $no_sttb = $this->input->post('no_sttb');
            $tgl_sttb = $this->input->post('tgl_sttb');
            $tempat_sttb = $this->input->post('tempat_sttb');
            $pilihan_studi = $this->input->post('pilihan_studi');
            $tahun_lulus = $this->input->post('tahun_lulus');
            $metode_pembayaran = $this->input->post('metode_pembayaran');

            date_default_timezone_set('Asia/Jakarta');
                $nmfile = "pmb_".time();
                $config['upload_path'] = './files/foto';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size'] = '20000';
                $config['file_name'] = $nmfile;
                // load library upload
                $this->load->library('upload', $config);
                // upload gambar 1
                $this->upload->do_upload('foto');
                $result1 = $this->upload->data();
                $result = array('gambar'=>$result1);
                $dfile = $result['gambar']['file_name'];

            $data = array(
                'nama_lengkap' => $nama,
                'tempat' => $tempat,
                'tgl_lahir' => $tgl,
                'alamat' => $alamat,
                'rt' => $rt,
                'rw' => $rw,
                'no_rumah' => $no_rumah,
                'kode_pos' => $kode_pos,
                'no_telp' => $no_telp,
                'tempat_tinggal' => $tempat_tinggal,
                'status_kawin' => $status_kawin,
                'jenis_pekerjaan' => $jenis_pekerjaan,
                'kewarganegaraan' => $kewarganegaraan,
                'agama' => $agama,
                'hobi' => $hobi,
                'slta' => $slta,
                'jurusan' => $jurusan,
                'no_sttb' => $no_sttb,
                'tgl_sttb' => $tgl_sttb,
                'tempat_sttb' => $tempat_sttb,
                'pilihan_studi' => $pilihan_studi,
                'tahun_lulus' => $tahun_lulus,
                'metode_pembayaran' => $metode_pembayaran,
                'foto' => $dfile,
                'tgl_buat' => date('Y-m-d'),
                'jam_buat' => date('H:i:s'),
            );
            $this->db->where('no_pendaftaran', $no_pendaftaran);
            $this->db->update('pendaftaran', $data);
            ?>
            <script type="text/javascript">
                alert('Data berhasil di ubah');
                window.location="<?php echo base_url() ?>app";
            </script>
            <?php
        }
        
        
        
    }

	public function simpan_formbukti()
	{
		$no_pendaftaran = $this->input->post('no_pendaftaran');
		$nama_lengkap = $this->input->post('nama_lengkap');
		$nama_pengirim = $this->input->post('nama_pengirim');
		
		date_default_timezone_set('Asia/Jakarta');
			$nmfile = "pmb_".time();
            $config['upload_path'] = './files/bukti';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = '20000';
            $config['file_name'] = $nmfile;
            // load library upload
            $this->load->library('upload', $config);
            // upload gambar 1
            $this->upload->do_upload('bukti_pembayaran');
            $result1 = $this->upload->data();
            $result = array('gambar'=>$result1);
            $dfile = $result['gambar']['file_name'];

        $data = array(
        	'no_pendaftaran' => $no_pendaftaran,
        	'nama_lengkap' => $nama_lengkap,
        	'nama_pengirim' => $nama_pengirim,
        	'bukti_pembayaran' => $dfile,
        );
        $this->db->insert('bukti_pembayaran', $data);
        ?>	
        <script type="text/javascript">
        	alert('Terima Kasih telah melakukan pembayaran, Silahkan Datang ke STIE Pontinak untuk mendapatkan no ujian.');
        	window.location="<?php echo base_url() ?>app"
        </script>
        <?php

	}

	public function selesai_daftar()
	{
		$data = array(
			'konten' => 'selesai_daftar',
		);
		$this->load->view('v_index', $data);
	}

	public function aksi_login()
	{
		

		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));

		//echo $username.' '.$password;
		$cek_user = $this->db->query("SELECT * FROM users WHERE username='$username' and password='$password' ");
		// $cek_kry = $this->db->query("SELECT * FROM karyawan WHERE username='$username' and password='$password' ");
		if ($cek_user->num_rows() == 1) {
			foreach ($cek_user->result() as $row) {
				$sess_data['id_user'] = $row->id_user;
				$sess_data['username'] = $row->username;
				$sess_data['level'] = $row->level;;
				$this->session->set_userdata($sess_data);
			}
			redirect('app');
		} else {
			?>
			<script type="text/javascript">
				alert('Username dan password kamu salah !');
				window.location="<?php echo base_url('app'); ?>";
			</script>
			<?php
		}

	}

	function logout()
	{
		$this->session->unset_userdata('id_user');
		$this->session->unset_userdata('username');
		session_destroy();
		redirect('app');
	}


}
