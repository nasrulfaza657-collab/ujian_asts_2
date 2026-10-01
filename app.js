const $ = s => document.querySelector(s);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function api(a, body, qs = '') {
  const r = await fetch(`api.php?action=${a}${qs}`, {
    method: body ? 'POST' : 'GET', headers: {'Content-Type': 'application/json'},
    body: body ? JSON.stringify(body) : undefined });
  const j = await r.json().catch(() => ({error: 'Server tidak merespons. Pastikan Apache & MySQL menyala.'}));
  if (r.status == 401 && j.logout && !location.pathname.endsWith('login.html')) {
    sessionStorage.setItem('msg', j.error); location = 'login.html';
  }
  return j;
}

function toast(m, t = 'info') {
  let w = $('#toasts');
  if (!w) { w = document.createElement('div'); w.id = 'toasts'; document.body.append(w); }
  const e = document.createElement('div'); e.className = 'toast ' + t; e.textContent = m; w.append(e);
  setTimeout(() => { e.classList.add('out'); setTimeout(() => e.remove(), 400); }, Math.min(12000, 4000 + m.length * 25));
}

// ---- Navbar + notifikasi (polling 5 detik) ----
let lastId = null;
function navbar(role, name) {
  $('#nav').innerHTML = `<div class="nav"><b>${role == 'admin' ? 'Panel Admin' : 'Dashboard Siswa'}</b>
    <div class="right"><span class="hide-m">${esc(name)}</span>
    <div class="bellw"><button class="bell" id="bell" aria-label="Notifikasi">🔔<i id="badge" hidden>0</i></button><div class="npanel" id="np"></div></div>
    <button class="btn light sm" id="out">Keluar</button></div></div>`;
  $('#bell').onclick = e => {
    e.stopPropagation(); $('#np').classList.toggle('show');
    if (!$('#badge').hidden) api('read', {}).then(() => $('#badge').hidden = true);
  };
  document.onclick = e => { if (!e.target.closest('.bellw')) $('#np').classList.remove('show'); };
  $('#out').onclick = async () => { await api('logout', {}); location = 'login.html'; };
  pollNotif(); setInterval(pollNotif, 5000);
}
async function pollNotif() {
  const r = await api('notifs'); if (!r.list) return;
  const fresh = r.list.filter(x => lastId !== null && x.id > lastId).reverse();
  const first = lastId === null ? r.list.filter(x => x.is_read == 0).slice(0, 3).reverse() : [];   // yang masuk saat belum login
  [...first, ...fresh].forEach(x => toast(x.message));
  lastId = Math.max(lastId ?? 0, r.list[0]?.id ?? 0);
  const unread = r.list.filter(x => x.is_read == 0).length, b = $('#badge');
  b.hidden = !unread; b.textContent = unread;
  $('#np').innerHTML = r.list.length
    ? r.list.map(x => `<div class="ni ${x.is_read == 0 ? 'new' : ''}"><p>${esc(x.message)}</p><small>${esc(x.created_at)}</small></div>`).join('')
    : '<div class="ni empty">Belum ada notifikasi.</div>';
  if (fresh.length) { $('#bell').classList.remove('ring'); void $('#bell').offsetWidth; $('#bell').classList.add('ring'); window.onNewNotif?.(); }
}

// ---- Form modal (dipakai admin & profil) ----
function openForm({title, data = {}, pwReq, onSave}) {
  const f = [['name','Nama lengkap'],['nisn','NISN'],['ttl','Tempat, tanggal lahir'],['email','Email','email'],['phone','No. HP','tel'],['address','Alamat'],['kelas','Kelas']];
  const m = document.createElement('div'); m.className = 'modal';
  m.innerHTML = `<form class="box"><h3>${esc(title)}</h3><div class="grid">
    ${f.map(([k,l,t]) => `<label>${l}<input name="${k}" type="${t||'text'}" value="${esc(data[k])}" ${k=='name'||k=='email'?'required':''}></label>`).join('')}
    <label>Gender<select name="gender"><option value="MALE">Laki-laki</option><option value="FEMALE">Perempuan</option></select></label>
    <label>${pwReq ? 'Password' : 'Password baru (kosongkan jika tidak diubah)'}<input name="password" type="password" minlength="6" ${pwReq ? 'required' : ''}></label>
    </div><div class="act"><button type="button" class="btn ghost">Batal</button><button class="btn">Simpan</button></div></form>`;
  document.body.append(m);
  const fm = m.querySelector('form');
  fm.gender.value = (data.gender || 'MALE').toUpperCase();
  fm.querySelector('.ghost').onclick = () => m.remove();
  fm.onsubmit = async e => {
    e.preventDefault();
    const r = await onSave(Object.fromEntries(new FormData(fm)));
    if (r.error) return toast(r.error, 'err');
    m.remove(); pollNotif();
  };
}