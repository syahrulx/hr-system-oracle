<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props from the controller
const props = defineProps({
  summary: Array,
  staffAttendance: Array,
  staffTasks: Array,
  staffHours: Array,
  staffLeaves: Array,
  staffRanking: Array,
  month: String,
  allStaff: Array,
  selectedStaffId: [String, Number],
});

// Icon SVGs for summary cards - Redesigned for more modern feel
const cardIcons = [
  `<svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>`, // Employees
  `<svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" /></svg>`, // Attendance Rate
  `<svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0V9.457c0-.621-.504-1.125-1.125-1.125h-.114M9.507 15.375V9.457c0-.621.504-1.125 1.125-1.125h.114m2.503 2.25V5.707c0-.621-.504-1.125-1.125-1.125h-.114M10.507 10.125V5.707c0-.621.504-1.125 1.125-1.125h.114M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h.114M17.25 21v-3.375c0-.621-.504-1.125-1.125-1.125h-.114M22.5 21s-1.5-6.75-10.5-6.75S1.5 21 1.5 21" /></svg>`, // Top Performer
];

const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

// Parse current month
const currentMonth = new Date(props.month + '-01');
const selectedMonthNum = ref(currentMonth.getMonth());
const showMonthDropdown = ref(false);

function selectMonth(idx) {
  selectedMonthNum.value = idx;
  showMonthDropdown.value = false;
  
  const newMonth = (idx + 1).toString().padStart(2, '0');
  const year = currentMonth.getFullYear();
  router.get(route('reports.index'), { month: `${year}-${newMonth}` }, { preserveState: true });
}

const selectedMonthLabel = computed(() => monthNames[selectedMonthNum.value]);

// Staff filter
const staffOptions = computed(() => {
  return [{ id: 'All Staff', name: 'All Staff' }, ...props.allStaff];
});

const selectedStaffName = ref(props.selectedStaffId ? 
  props.allStaff.find(s => s.id == props.selectedStaffId)?.name || 'All Staff' : 
  'All Staff'
);

function selectStaff(e) {
  const staffId = e.target.value === 'All Staff' ? null : 
    props.allStaff.find(s => s.name === e.target.value)?.id;
  
  router.get(route('reports.index'), { 
    month: props.month,
    staff_id: staffId 
  }, { preserveState: true });
}

// Medal icons for top 3
const medalIcons = [
  `<svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>`,
  `<svg class="w-6 h-6 text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>`,
  `<svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>`
];
</script>

<template>
  <Head title="Reports & Analytics" />
  <AuthenticatedLayout>
    <div class="min-h-screen bg-[#030711] text-slate-200 py-10 px-4 md:px-8">
      <div class="max-w-7xl mx-auto space-y-10 animate-fade-in-up">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-2 border-b border-slate-800/50">
          <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-slate-500 bg-clip-text text-transparent">
              Operational Insights
            </h1>
            <p class="text-slate-400 text-sm mt-1">Detailed performance analysis for {{ selectedMonthLabel }} {{ currentMonth.getFullYear() }}</p>
          </div>
          
          <div class="flex items-center gap-3">
            <div class="relative">
              <button 
                @click="showMonthDropdown = !showMonthDropdown" 
                class="glass-panel px-4 py-2 rounded-xl flex items-center gap-2 hover:bg-slate-800/50 transition-all border border-slate-700/50 text-sm font-medium"
              >
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg>
                {{ selectedMonthLabel }}
                <svg :class="{'rotate-180': showMonthDropdown}" class="w-4 h-4 ml-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
              </button>
              
              <div v-if="showMonthDropdown" class="absolute right-0 mt-2 w-48 glass-panel rounded-xl shadow-2xl z-50 overflow-hidden animate-slide-in">
                <div class="py-1 max-h-60 overflow-y-auto custom-scrollbar">
                  <button 
                    v-for="(name, idx) in monthNames" 
                    :key="name"
                    @click="selectMonth(idx)" 
                    class="w-full text-left px-4 py-3 text-sm hover:bg-white/5 transition-colors"
                    :class="{ 'bg-blue-500/20 text-blue-400': selectedMonthNum === idx }"
                  >
                    {{ name }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="(card, i) in props.summary" :key="card.label" class="summary-card glass-panel group">
            <div class="p-6 relative overflow-hidden">
              <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-500" v-html="cardIcons[i]"></div>
              <div class="flex items-center gap-4 mb-4">
                <div class="p-2.5 rounded-xl bg-slate-800/50 text-white" v-html="cardIcons[i]"></div>
                <div class="text-slate-400 text-sm font-medium">{{ card.label }}</div>
              </div>
              <div class="text-3xl font-bold text-white mb-2">{{ card.value }}</div>
              <div class="h-1 w-12 rounded-full bg-blue-500 group-hover:w-full transition-all duration-700"></div>
            </div>
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          <!-- Attendance Breakdown (Left Column) -->
          <div class="lg:col-span-12 xl:col-span-7 space-y-6">
            <div class="glass-panel p-6 flex flex-col h-full">
              <div class="flex items-center justify-between mb-8">
                <div>
                  <h3 class="text-xl font-bold text-white">Attendance Distribution</h3>
                  <p class="text-slate-400 text-xs mt-1">Employee punctuality and presence mapping</p>
                </div>
                <div class="flex items-center gap-2">
                   <select @change="selectStaff" v-model="selectedStaffName" class="glass-input text-xs">
                     <option v-for="option in staffOptions" :key="option.id" :value="option.name">{{ option.name }}</option>
                   </select>
                </div>
              </div>

              <div class="space-y-8 flex-grow">
                <div v-for="staff in props.staffAttendance" :key="staff.name" class="staff-row">
                  <div class="flex justify-between items-end mb-2">
                    <span class="text-sm font-semibold text-slate-300">{{ staff.name }}</span>
                    <span class="text-[10px] text-slate-500 font-mono tracking-tighter uppercase">Records: {{ (staff.present + staff.late + staff.absent) }}</span>
                  </div>
                  <div class="attendance-track">
                    <div 
                      class="track-segment segment-present" 
                      :style="{ width: `${(staff.present / (staff.present + staff.late + staff.absent) * 100) || 0}%` }"
                      v-tooltip="'Present: ' + staff.present"
                    ></div>
                    <div 
                      class="track-segment segment-late" 
                      :style="{ width: `${(staff.late / (staff.present + staff.late + staff.absent) * 100) || 0}%` }"
                      v-tooltip="'Late: ' + staff.late"
                    ></div>
                    <div 
                      class="track-segment segment-absent" 
                      :style="{ width: `${(staff.absent / (staff.present + staff.late + staff.absent) * 100) || 0}%` }"
                      v-tooltip="'Absent: ' + staff.absent"
                    ></div>
                  </div>
                </div>
              </div>

              <div class="mt-10 pt-6 border-t border-slate-800 flex justify-center gap-8 text-[11px] font-bold tracking-widest uppercase text-slate-400">
                <div class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></span> Present
                </div>
                <div class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full bg-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.3)]"></span> Late
                </div>
                <div class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.3)]"></span> Absent
                </div>
              </div>
            </div>
          </div>

          <!-- Leave Balances (Right Column) -->
          <div class="lg:col-span-12 xl:col-span-5 space-y-6">
            <div class="glass-panel p-6">
              <div class="mb-6">
                <h3 class="text-xl font-bold text-white">Leave Intelligence</h3>
                <p class="text-slate-400 text-xs mt-1">Current balances and monthly utlization</p>
              </div>

              <div class="grid grid-cols-1 gap-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                <div v-for="staff in props.staffLeaves" :key="staff.name" class="leave-card glass-panel group p-4 border-slate-800/50 hover:border-slate-700 transition-colors">
                  <div class="flex justify-between items-start mb-4">
                    <span class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">{{ staff.name }}</span>
                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase">
                      <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                      </span>
                      {{ staff.approved }} Approved
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-3 gap-3">
                    <div class="balance-pill">
                      <span class="text-[9px] text-slate-500 uppercase font-bold mb-1">Annual</span>
                      <span class="text-sm font-bold text-blue-400">{{ staff.annual_balance }}</span>
                    </div>
                    <div class="balance-pill">
                      <span class="text-[9px] text-slate-500 uppercase font-bold mb-1">Sick</span>
                      <span class="text-sm font-bold text-amber-500">{{ staff.sick_balance }}</span>
                    </div>
                    <div class="balance-pill">
                      <span class="text-[9px] text-slate-500 uppercase font-bold mb-1">Extra</span>
                      <span class="text-sm font-bold text-rose-500">{{ staff.emergency_balance }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Ranking Table (Bottom Full Width) -->
        <div class="glass-panel overflow-hidden border-slate-800/70">
          <div class="p-6 bg-white/[0.02] border-b border-slate-800 flex justify-between items-center">
            <div>
              <h3 class="text-xl font-bold text-white">Efficiency Ranking</h3>
              <p class="text-slate-400 text-xs mt-1">Algorithmic score based on attendance weight (0.7x)</p>
            </div>
            <button class="text-xs bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-blue-900/20 active:scale-95">
              Export Analysis
            </button>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead>
                <tr class="bg-slate-900/50 text-[10px] text-slate-400 font-black uppercase tracking-widest border-b border-slate-800/80">
                  <th class="px-8 py-5">Rank</th>
                  <th class="px-6 py-5">Associate Name</th>
                  <th class="px-6 py-5">Performance Index</th>
                  <th class="px-8 py-5 text-right">Composite Score</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/50">
                <tr v-for="(row, idx) in props.staffRanking" :key="row.name" class="hover:bg-white/[0.02] transition-colors group">
                  <td class="px-8 py-4">
                    <div class="flex items-center gap-3">
                      <div v-if="idx < 3" v-html="medalIcons[idx]"></div>
                      <span v-else class="w-6 text-center font-mono text-slate-500">{{ idx + 1 }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="font-bold text-slate-200 group-hover:text-white transition-colors">{{ row.name }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                      <div class="w-48 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out"
                          :style="{ 
                             width: row.attendance + '%', 
                             background: row.attendance >= 90 ? '#10b981' : row.attendance >= 80 ? '#fbbf24' : '#f43f5e' 
                          }">
                        </div>
                      </div>
                      <span class="text-xs font-mono font-bold" :class="[row.attendance >= 90 ? 'text-emerald-400' : row.attendance >= 80 ? 'text-amber-400' : 'text-rose-400']">
                        {{ row.attendance }}%
                      </span>
                    </div>
                  </td>
                  <td class="px-8 py-4 text-right">
                    <span class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 text-white font-mono font-bold text-xs ring-1 ring-white/5 shadow-inner">
                      {{ row.score }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:deep(*) {
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.glass-panel {
  background: rgba(13, 17, 23, 0.7);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(51, 65, 85, 0.4);
  border-radius: 1.5rem;
}

.summary-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(51, 65, 85, 0.3);
}

.summary-card:hover {
  transform: translateY(-5px);
  background: rgba(30, 41, 59, 0.5);
  border-color: rgba(96, 165, 250, 0.4);
  box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.5), 0 0 20px rgba(59, 130, 246, 0.1);
}

.attendance-track {
  height: 0.75rem;
  background: rgba(30, 41, 59, 0.8);
  border-radius: 9999px;
  display: flex;
  overflow: hidden;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
}

.track-segment {
  height: 100%;
  transition: width 1.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.segment-present { background: #10b981; }
.segment-late { background: #fbbf24; }
.segment-absent { background: #f43f5e; }

.balance-pill {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.75rem 0.5rem;
  background: rgba(15, 23, 42, 0.5);
  border-radius: 1rem;
  border: 1px solid rgba(51, 65, 85, 0.2);
}

.glass-input {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(51, 65, 85, 0.5);
  border-radius: 0.75rem;
  color: white;
  padding: 0.5rem 1rem;
  font-weight: 600;
  outline: none;
  transition: all 0.3s;
}

.glass-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.2);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(51, 65, 85, 0.5);
  border-radius: 10px;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fade-in-up {
  0% { transform: translateY(20px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

@keyframes slide-in {
  0% { transform: translateY(10px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

font-mono {
  font-family: 'JetBrains Mono', monospace !important;
}
</style>