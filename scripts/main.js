let currentStoryText = '';

function formatBold(text) {
  if (!text) return '';
  const escaped = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
  return escaped.replace(/\n{2,}/g, '\n\n').replace(/\n/g, '<br>');
}

const btnGenerate = document.getElementById('posalji');
const rezultat = document.getElementById('rezultat');
const rezimeRoditelji = document.getElementById('rezime-roditelji');
const ratingEl = document.getElementById('rating');
const shareBtn = document.getElementById('shareBtn');

shareBtn.addEventListener('click', async () => {
  const rating = ratingEl.value;
  if (!rating) {
    alert('Molimo, prvo ocenite priču pre deljenja.');
    return;
  }

  const ime = document.getElementById('ime').value.trim();
  const vrsta = document.getElementById('vrsta').value;
  const ton = document.getElementById('ton').value;
  const uzrast = document.getElementById('uzrast').value;
  const tema = document.getElementById('tema').value;

  const storyText = document.getElementById('rezultat').innerText || '';
  const parentMsg = document.getElementById('rezime-roditelji').innerText || '';

  try {
    const logResponse = await fetch('log_share.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ime,
        vrsta,
        ton,
        uzrast,
        tema,
        prica: storyText,
        rezimeRoditelji: parentMsg,
        ocena: rating
      })
    });
    const logData = await logResponse.json();
    if (logData.success) {
      const shareText = `Pročitaj ovu bajku na DreamTales:\n\n${storyText}\n\nOcena: ${rating} ⭐`;

      if (navigator.share) {
        navigator.share({
          title: 'DreamTales - Personalizovana priča',
          text: shareText,
          url: window.location.href
        }).catch(console.error);
      } else {
        await navigator.clipboard.writeText(shareText);
        alert('Tekst priče i ocena su kopirani u clipboard. Možeš sada da ih podeliš!');
      }
    } else {
      alert('Greška pri beleženju priče: ' + (logData.error || 'Nepoznata greška'));
    }
  } catch (err) {
    alert('Greška pri povezivanju sa serverom.');
  }
});

btnGenerate.addEventListener('click', async () => {
  const ime = document.getElementById('ime').value.trim();
  const vrsta = document.getElementById('vrsta').value;
  const ton = document.getElementById('ton').value;
  const uzrast = document.getElementById('uzrast').value;
  const tema = document.getElementById('tema').value;

  if (!ime) {
    alert('Unesi ime deteta!');
    return;
  }

  ratingEl.disabled = true;
  shareBtn.disabled = true;
  ratingEl.value = '';
  currentStoryText = '';

  rezultat.innerHTML = '<p style="color:#9a6b52">⏳ Pravim tvoju bajku...</p>';
  rezimeRoditelji.innerHTML = '<h2>Poruka za roditelje</h2><p style="color:#7a4a00">⏳ Pripremam sažetak...</p>';

  const promptPrica = `Napiši kratku priču za spavanje za dete po imenu ${ime}. Vrsta priče: ${vrsta}. Ton priče: ${ton}. Uzrast deteta: ${uzrast}. Tema ili pouka priče: ${tema}. Priča treba da bude nežna, zanimljiva i edukativna, pogodna za laku noć. Samo priča — nemoj dodavati uvodne rečenice poput "Evo jedne priče..." ili bilo kakve dodatne napomene.`;

  try {
    const responsePrica = await fetch('generate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ prompt: promptPrica })
    });

    const dataPrica = await responsePrica.json();

    if (dataPrica.error) {
      let errorMsg = '';
      if (typeof dataPrica.error === 'string') errorMsg = dataPrica.error;
      else if (dataPrica.error.message) errorMsg = dataPrica.error.message;
      else errorMsg = JSON.stringify(dataPrica.error);
      rezultat.innerHTML = `<p style="color:#b33">❌ Greška: ${errorMsg}</p>`;
      rezimeRoditelji.innerHTML = '<h2>Poruka za roditelje</h2><p style="color:#7a4a00"></p>';
      return;
    }

    const pricaTekst = (dataPrica.candidates && dataPrica.candidates[0]?.content?.parts[0]?.text)
      ? dataPrica.candidates[0].content.parts[0].text.trim()
      : (dataPrica.output?.[0]?.content?.[0]?.text ?? '');

    if (!pricaTekst) {
      rezultat.innerHTML = '<p style="color:#b33">❌ Nema rezultata od AI-ja.</p>';
      rezimeRoditelji.innerHTML = '<h2>Poruka za roditelje</h2><p style="color:#7a4a00"></p>';
      return;
    }

    currentStoryText = pricaTekst;
    rezultat.innerHTML = `<div>${formatBold(pricaTekst)}</div>`;

    const promptRoditelji = `Pročitaj sledeću priču i napiši kratak, jasan sažetak i glavnu poruku/pouku koju roditelj treba da prenese detetu.\n\n${pricaTekst}\n\nSažetak i pouka trebaju biti koncizni (1-3 rečenice), bez uvoda poput "Evo sažetka...".`;

    const responseRod = await fetch('generate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ prompt: promptRoditelji })
    });

    const dataRod = await responseRod.json();

    if (dataRod.error) {
      let errorMsg = '';
      if (typeof dataRod.error === 'string') errorMsg = dataRod.error;
      else if (dataRod.error.message) errorMsg = dataRod.error.message;
      else errorMsg = JSON.stringify(dataRod.error);
      rezimeRoditelji.innerHTML = `<h2>Poruka za roditelje</h2><p style="color:#b33">❌ Greška: ${errorMsg}</p>`;
    } else {
      const rezTekst = (dataRod.candidates && dataRod.candidates[0]?.content?.parts[0]?.text)
        ? dataRod.candidates[0].content.parts[0].text.trim()
        : (dataRod.output?.[0]?.content?.[0]?.text ?? '');
      if (!rezTekst) {
        rezimeRoditelji.innerHTML = `<h2>Poruka za roditelje</h2><p style="color:#7a4a00">Nema sažetka.</p>`;
      } else {
        rezimeRoditelji.innerHTML = `<h2>Poruka za roditelje</h2><p>${formatBold(rezTekst)}</p>`;
      }
    }

    ratingEl.disabled = false;
    shareBtn.disabled = false;
    ratingEl.focus();

  } catch (err) {
    console.error(err);
    rezultat.innerHTML = '<p style="color:#b33">❌ Greška pri komunikaciji sa serverom.</p>';
    rezimeRoditelji.innerHTML = '<h2>Poruka za roditelje</h2><p style="color:#7a4a00">Greška pri komunikaciji.</p>';
  }
});
