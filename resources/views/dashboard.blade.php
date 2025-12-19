@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Dashboard CIATS</h2>
        <p class="text-muted">Selamat datang, {{ $user['name'] }}!</p>
    </div>
</div>

<div class="row">
    <!-- TOTAL USER -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title">Total User</h6>
                    <h2 id="totalUsers">0</h2>
                </div>
                <i class="bi bi-people fs-1"></i>
            </div>
        </div>
    </div>

    <!-- ROLE -->
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title">Role Anda</h6>
                    <h4 class="text-uppercase">{{ $user['role'] }}</h4>
                </div>
                <i class="bi bi-person-badge fs-1"></i>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="bi bi-lightning-charge"></i>
    Dashboard ini <strong>REAL-TIME</strong> menggunakan Firebase Realtime Database
</div>
@endsection


{{-- =============================== --}}
{{-- FIREBASE REALTIME CONFIG & LOGIC --}}
{{-- =============================== --}}
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-database.js";

/* ===============================
   FIREBASE CONFIG (PUNYA KAMU)
   =============================== */
const firebaseConfig = {
  apiKey: "AIzaSyBthtd03Sez9mKwvPFCWhJRXis0y_6DT-Y",
  authDomain: "ciats-9545b.firebaseapp.com",
  databaseURL: "https://ciats-9545b-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "ciats-9545b",
  storageBucket: "ciats-9545b.firebasestorage.app",
  messagingSenderId: "895888204902",
  appId: "1:895888204902:web:fde4c84d6dcc0fbcb0b9a7"
};

// Init Firebase
const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

/* ===============================
   REALTIME TOTAL USER
   =============================== */
const usersRef = ref(database, 'users');

onValue(usersRef, (snapshot) => {
    const users = snapshot.val();

    // Hitung jumlah user
    const totalUser = users ? Object.keys(users).length : 0;

    document.getElementById('totalUsers').textContent = totalUser;
});
</script>
