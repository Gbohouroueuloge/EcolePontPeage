<?php
$title = "Operators";

?>

<main class="md:ml-72 pt-20 px-8 pb-12 relative">
  <div>
    <!-- Header Section with Editorial Typography -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-2 mb-10">
      <div>
        <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
          Système
        </p>
        <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">
          Gestion des Opérateurs
        </h2>
      </div>
      <button
        class="bg-primary text-white px-6 py-3 rounded-lg font-bold flex items-center gap-2 gold-glow hover:bg-secondary transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter un agent
      </button>
    </div>

    <!-- Compact Stats Row (Bento Style) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-primary">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Total Staff</div>
        <div class="font-mono text-3xl font-bold text-primary">124</div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-secondary-container">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Postes Actifs</div>
        <div class="font-mono text-3xl font-bold text-primary">32</div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-emerald-500">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Taux d'Efficacité</div>
        <div class="font-mono text-3xl font-bold text-primary">98.2<span class="text-sm font-body">%</span></div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-on-tertiary-container">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Passages/Heure</div>
        <div class="font-mono text-3xl font-bold text-primary">4,102</div>
      </div>
    </div>

    <!-- Main Data Grid -->
    <div class="bg-surface-container-lowest rounded-xl monolith-shadow overflow-hidden">
      <div class="flex bg-surface-container-low p-1 rounded-lg">
        <button class="px-4 py-2 text-xs font-bold bg-white shadow-sm rounded-md text-primary">Tous</button>
        <button class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-primary">En service</button>
        <button class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-primary">Repos</button>
      </div>

      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-low">
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Agent Details</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Status</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Voie Assignée</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Passages
              Mensuels</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Dernière Garde</th>
            <th class="px-6 py-4"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low">
          <!-- Row 1 -->
          <tr class="hover:bg-surface-container-low/30 transition-colors group cursor-pointer">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-md overflow-hidden bg-slate-100 border border-surface-variant">
                  <img alt="Agent Jean-Pierre" class="w-full h-full object-cover"
                    data-alt="portrait of a focused professional security agent in a neat uniform"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuB1kwItSqblapgvVQI40kig6Mr7lZaV-FcPYShSMgZgCuzu5tkGQfmZxnlMeOdZTykiWXnt4KQI9SCfLv9bHhMQyqsCbn1U319WsGDxzkukyTJMizRrEwlvmz0OrUk3nADTD7uQqoT-i-oMsYQV9-sVr7ZafMhdcs-iLu-nqCY8hcqXx-6Y6t38DRZtjI6pkEt6CMEfQShhzQeLponcKJpD5eQRwWYewC5MqTanvRRhGZ9n2KDO_AZP99x4K705EXiGJowtPGMu8HJ2" />
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase">Jean-Pierre Dubois</div>
                  <div class="font-mono text-[10px] text-slate-400">ID-PRT-22940</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 diamond-indicator bg-emerald-500 animate-pulse"></div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase">En Service</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span
                class="bg-primary-container text-white px-2 py-1 rounded text-[10px] font-mono tracking-tighter">LANE_04_NORTH</span>
            </td>
            <td class="px-6 py-4 text-right">
              <span class="font-mono text-sm font-bold text-primary">12,450</span>
            </td>
            <td class="px-6 py-4 text-xs text-slate-500">
              Aujourd'hui, 06:00
            </td>
            <td class="px-6 py-4 text-right">
              <span
                class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">chevron_right</span>
            </td>
          </tr>
          <!-- Row 2 -->
          <tr class="hover:bg-surface-container-low/30 transition-colors group cursor-pointer">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-md overflow-hidden bg-slate-100 border border-surface-variant">
                  <img alt="Agent Sarah K." class="w-full h-full object-cover"
                    data-alt="portrait of a professional female officer in bridge operations gear"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKuNv3cLQF-PiPZNjL2AkeDHD-W1Qk3EqxkezE9KdEcp-f6E8T7_0_oG9mbO82qhxA83BHfb9Fr_wq0TjuO9GJHZcsAvBqPvp8cW7S_v1u8dUyDA4y8upZkCeZhO1qj0NEx9MQ7doF4pRfB3VxL-VsKPr3Udm5orc7uwdhuWkvjEX2LimqJJa4HjeNInpir21Uclm_1pMUY-sgBR3RHOc6uiTHz09sQkNxoqFJYSv3pc-27vK_3tVQEiURGuRiu-c1OX9pe9NjWuRs" />
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase">Sarah Kerkouane</div>
                  <div class="font-mono text-[10px] text-slate-400">ID-PRT-23112</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 diamond-indicator bg-slate-300"></div>
                <span class="text-[11px] font-bold text-slate-500 uppercase">Repos</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span
                class="bg-surface-container-high text-primary px-2 py-1 rounded text-[10px] font-mono tracking-tighter">—</span>
            </td>
            <td class="px-6 py-4 text-right">
              <span class="font-mono text-sm font-bold text-primary">11,892</span>
            </td>
            <td class="px-6 py-4 text-xs text-slate-500">
              Hier, 22:00
            </td>
            <td class="px-6 py-4 text-right">
              <span
                class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">chevron_right</span>
            </td>
          </tr>
          <!-- Row 3 -->
          <tr class="hover:bg-surface-container-low/30 transition-colors group cursor-pointer">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-md overflow-hidden bg-slate-100 border border-surface-variant">
                  <img alt="Agent Marc T." class="w-full h-full object-cover"
                    data-alt="middle-aged male staff member in logistics uniform smiling professionally"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4Z08acE9a37acjFfi_6dGuOxNFuzEU0styaAGVa5NrpUtxjzlXluZh1vbm5F91uiNi-JuxZMbJeYJDyZFSyPx4LK7KYQBHEChmaSIN4TIr8Bj-M9nREPos4uavdd3qhb2F-NIYXVmSAU94DIVt6a68bQLw-ybJlU5Wtl0B1EetKj40812nQUxpA0KmEVPpccaluSIZbXOSMt5NkMbGQFlcPzc9Ja9wGb0jAl4ZKmEZPbjQVC3njM_0wtNkbgpEYx-zE8TwR99YG8p" />
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase">Marc Toussaint</div>
                  <div class="font-mono text-[10px] text-slate-400">ID-PRT-22005</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 diamond-indicator bg-emerald-500 animate-pulse"></div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase">En Service</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span
                class="bg-primary-container text-white px-2 py-1 rounded text-[10px] font-mono tracking-tighter">LANE_01_SOUTH</span>
            </td>
            <td class="px-6 py-4 text-right">
              <span class="font-mono text-sm font-bold text-primary">14,105</span>
            </td>
            <td class="px-6 py-4 text-xs text-slate-500">
              Aujourd'hui, 08:00
            </td>
            <td class="px-6 py-4 text-right">
              <span
                class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">chevron_right</span>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="px-6 py-4 bg-surface-container-low/30 flex justify-between items-center">
        <span class="text-xs font-bold text-slate-500">Affichage de 1-3 sur 124 agents</span>
        <div class="flex gap-2">
          <button class="p-2 bg-white rounded border border-surface-variant text-primary hover:bg-slate-50">
            <span class="material-symbols-outlined text-xs">chevron_left</span>
          </button>
          <button class="p-2 bg-white rounded border border-surface-variant text-primary hover:bg-slate-50">
            <span class="material-symbols-outlined text-xs">chevron_right</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Slide-in Side Panel (Agent Details) -->
  <div
    class="fixed hidden right-0 top-0 h-full w-110 bg-white z-60 shadow-2xl monolith-shadow flex flex-col translate-x-0 transition-transform duration-300 border-l border-surface-container">
    <!-- Panel Header -->
    <div class="px-8 py-10 border-b border-surface-container-low relative">
      <button class="absolute top-8 right-8 text-slate-400 hover:text-primary">
        <span class="material-symbols-outlined">close</span>
      </button>
      <div class="flex items-start gap-6">
        <div class="w-24 h-24 rounded-lg bg-surface-container border-2 border-secondary-container overflow-hidden p-1">
          <img alt="Detail Agent Jean-Pierre" class="w-full h-full object-cover rounded"
            data-alt="focused detailed portrait of a senior toll bridge operator"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqrlKH7p2trhedP0Fh2Hx59OkMv7ijI9MCS2tIX4kkIHrVlptZZyH1mgU7bwCt26o6s4peTTBde_TN6_q6OeXAqoP_KCia0OQFoJoWx8OhjpB97XRRn1jDZhwE1Yda6cDl8h5qFXdyxjsr3GNzvW4pt_MHmNxY-aM9ZbtW4bKRXRkrKO2vmeIaD_rUoKfQg0on8YnBF1nK3Z3vE8fTGPjZODRoVy7IHWerkdM8Y-tA2uTc2cblJf63W68IFu7bKVSlm16u00gaGNe9" />
        </div>
        <div class="mt-2">
          <span class="text-[10px] font-mono bg-primary text-white px-2 py-0.5 rounded mb-2 inline-block">SENIOR
            OFFICER</span>
          <h3 class="font-['Outfit'] text-2xl font-bold tracking-tight text-primary">Jean-Pierre Dubois</h3>
          <div class="flex items-center gap-2 mt-1">
            <div class="w-1.5 h-1.5 diamond-indicator bg-emerald-500"></div>
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-tighter">Affectation Actuelle: Voie 4
              Nord</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Panel Body -->
    <div class="flex-1 overflow-y-auto p-8 space-y-10">
      <!-- Key Metrics Grid -->
      <div>
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
          <span class="w-4 h-px bg-slate-300"></span> Performances du mois
        </h4>
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-surface-container-low p-4 rounded-lg border-l-2 border-primary">
            <div class="text-[10px] text-slate-500 font-bold uppercase mb-1">Passages Validés</div>
            <div class="font-mono text-xl font-bold text-primary">12,450</div>
          </div>
          <div class="bg-surface-container-low p-4 rounded-lg border-l-2 border-secondary">
            <div class="text-[10px] text-slate-500 font-bold uppercase mb-1">Anomalies Signalées</div>
            <div class="font-mono text-xl font-bold text-primary">02</div>
          </div>
        </div>
      </div>
      <!-- Recent Shifts List -->
      <div>
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
          <span class="w-4 h-px bg-slate-300"></span> Historique des gardes
        </h4>
        <div class="space-y-4">
          <div class="flex items-center justify-between py-3 border-b border-surface-container-low">
            <div>
              <div class="text-sm font-bold text-primary">Matin (06:00 - 14:00)</div>
              <div class="text-[10px] text-slate-400 uppercase font-mono">15 Octobre 2023 • LANE_04</div>
            </div>
            <div class="text-right">
              <div class="font-mono text-sm font-bold text-primary">824 p.</div>
              <div class="text-[9px] text-emerald-600 font-bold uppercase">Optimal</div>
            </div>
          </div>
          <div class="flex items-center justify-between py-3 border-b border-surface-container-low">
            <div>
              <div class="text-sm font-bold text-primary">Nuit (22:00 - 06:00)</div>
              <div class="text-[10px] text-slate-400 uppercase font-mono">14 Octobre 2023 • LANE_01</div>
            </div>
            <div class="text-right">
              <div class="font-mono text-sm font-bold text-primary">410 p.</div>
              <div class="text-[9px] text-emerald-600 font-bold uppercase">Optimal</div>
            </div>
          </div>
          <div class="flex items-center justify-between py-3 border-b border-surface-container-low">
            <div>
              <div class="text-sm font-bold text-primary">Après-midi (14:00 - 22:00)</div>
              <div class="text-[10px] text-slate-400 uppercase font-mono">13 Octobre 2023 • LANE_04</div>
            </div>
            <div class="text-right">
              <div class="font-mono text-sm font-bold text-primary">912 p.</div>
              <div class="text-[9px] text-emerald-600 font-bold uppercase">Optimal</div>
            </div>
          </div>
        </div>
      </div>
      <!-- Additional Info -->
      <div class="bg-primary p-6 rounded-xl text-white">
        <div class="flex items-center gap-4 mb-4">
          <span class="material-symbols-outlined text-secondary-container"
            style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
          <span class="text-xs font-bold uppercase tracking-widest">Note d'Assiduité</span>
        </div>
        <div class="font-['Outfit'] text-4xl font-bold mb-2">9.8<span class="text-lg opacity-50">/10</span></div>
        <p class="text-[11px] text-slate-400 font-light leading-relaxed">Jean-Pierre est un agent exemplaire avec un
          taux de présence de 100% sur les 90 derniers jours.</p>
      </div>
    </div>
    <!-- Panel Footer -->
    <div class="p-8 border-t border-surface-container-low bg-surface-container-lowest grid grid-cols-2 gap-4">
      <button
        class="px-6 py-3 border border-surface-variant rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-colors">Modifier
        Profil</button>
      <button
        class="px-6 py-3 bg-secondary text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-primary transition-colors">Assigner
        Garde</button>
    </div>
  </div>
</main>