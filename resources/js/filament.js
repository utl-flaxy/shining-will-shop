// resources/js/filament.js
// 既に window.Alpine がある場合はそれを使い、無ければ dynamic import で読み込む
(async () => {
  // プラグインを登録するユーティリティ
  const registerPlugins = (Alpine) => {
    try {
      // persist/focus をウィンドウが持っていればそれを使う（Vite側でバンドルされている可能性）
      // ただし dynamic import で確実にモジュールを取得しておく
      // @alpinejs/focus と @alpinejs/persist をグローバルに登録
      // NOTE: もしこれらが既にグローバルで読み込まれているなら二重登録は harmless ですが注意
      import('@alpinejs/focus').then((mod) => {
        Alpine.plugin(mod.default || mod);
      }).catch(() => { /* ignore */ });

      import('@alpinejs/persist').then((mod) => {
        Alpine.plugin(mod.default || mod);
      }).catch(() => { /* ignore */ });
    } catch (e) {
      console.warn('Failed to register Alpine plugins:', e);
    }
  };

  if (typeof window !== 'undefined' && window.Alpine) {
    // 既にグローバル Alpine がある場合はそれを利用
    registerPlugins(window.Alpine);
    // 保険で window.Alpine.version をログ（デバッグ用、不要なら削る）
    console.log('Using existing Alpine instance', window.Alpine?.version);
  } else {
    // グローバルにない場合はモジュールとして読み込んで初期化する
    const AlpineModule = await import('alpinejs');
    const Alpine = AlpineModule.default || AlpineModule;
    registerPlugins(Alpine);
    window.Alpine = Alpine;
    Alpine.start();
    console.log('Started Alpine from filament.js', Alpine?.version);
  }
})();
