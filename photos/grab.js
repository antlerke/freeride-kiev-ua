// Вставляється в консоль сторінки Gemini. Бере останнє велике зображення
// у відповіді й відправляє його в локальний приймач (photos/recv.py).
// Нічого не читає, крім самої картинки, і нікуди більше не шле.
window.frGrab = async function (shot) {
  const imgs = [...document.querySelectorAll('img')]
    .filter(i => i.naturalWidth >= 512 && i.naturalHeight >= 512);
  if (!imgs.length) throw new Error('великих зображень на сторінці немає');
  const img = imgs[imgs.length - 1];
  const blob = await (await fetch(img.currentSrc || img.src)).blob();
  const r = await fetch('http://127.0.0.1:8778/save?name=' + encodeURIComponent(shot), {
    method: 'POST',
    body: blob,
    headers: { 'Content-Type': blob.type || 'image/png' },
  });
  return shot + ' → ' + (await r.text()) + ' · ' +
         img.naturalWidth + '×' + img.naturalHeight + ' · ' +
         Math.round(blob.size / 1024) + ' КБ';
};
