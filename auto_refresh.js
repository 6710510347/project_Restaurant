function autoRefresh(fn, intervalMs = 10000, skipIf) {
  let running = false;

  async function tick() {
    if (running || document.hidden) return;
    if (skipIf && skipIf()) return;
    running = true;
    try { await fn(); }
    catch (e) { console.error('auto refresh:', e); }
    finally { running = false; }
  }

  const timer = setInterval(tick, intervalMs);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) tick(); });
  return () => clearInterval(timer);
}