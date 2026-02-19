
// Definir componente globalmente para garantir acesso via x-data
window.kanbanBoard = function (ordersData, startDate) {
    return {
        view: 'kanban', // 'kanban' | 'calendar'
        calendarView: 'month', // 'month' | 'week' | 'day'
        currentDate: startDate ? new Date(startDate + 'T12:00:00') : new Date(),
        events: ordersData,

        get currentMonthName() {
            if (this.calendarView === 'day') {
                return this.currentDate.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
            }
            if (this.calendarView === 'week') {
                const start = new Date(this.currentDate);
                start.setDate(this.currentDate.getDate() - this.currentDate.getDay());
                const end = new Date(start);
                end.setDate(start.getDate() + 6);

                if (start.getMonth() === end.getMonth()) {
                    return `${start.getDate()} - ${end.getDate()} de ${start.toLocaleString('pt-BR', { month: 'long', year: 'numeric' })} `;
                } else {
                    return `${start.getDate()} de ${start.toLocaleString('pt-BR', { month: 'short' })} - ${end.getDate()} de ${end.toLocaleString('pt-BR', { month: 'short', year: 'numeric' })} `;
                }
            }
            return this.currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
        },

        get calendarDays() {
            const days = [];
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();

            if (this.calendarView === 'day') {
                days.push({
                    date: new Date(this.currentDate),
                    isCurrentMonth: true,
                    isToday: this.isToday(this.currentDate)
                });
                return days;
            }

            if (this.calendarView === 'week') {
                const current = new Date(this.currentDate);
                const day = current.getDay(); // 0 (Domingo) - 6 (Sábado)
                const startOfWeek = new Date(current);
                startOfWeek.setDate(current.getDate() - day);

                for (let i = 0; i < 7; i++) {
                    const d = new Date(startOfWeek);
                    d.setDate(startOfWeek.getDate() + i);
                    days.push({
                        date: d,
                        isCurrentMonth: d.getMonth() === month,
                        isToday: this.isToday(d)
                    });
                }
                return days;
            }

            // Month View (Default)
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDayOfWeek = firstDay.getDay();

            // Dias do mês anterior
            for (let i = startDayOfWeek; i > 0; i--) {
                const d = new Date(year, month, 1 - i);
                days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
            }

            // Dias do mês atual
            for (let i = 1; i <= lastDay.getDate(); i++) {
                const d = new Date(year, month, i);
                days.push({ date: d, isCurrentMonth: true, isToday: this.isToday(d) });
            }

            // Preencher grade (42 células = 6 linhas)
            const remainingCells = 42 - days.length;
            for (let i = 1; i <= remainingCells; i++) {
                const d = new Date(year, month + 1, i);
                days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
            }

            return days;
        },

        isToday(date) {
            const today = new Date();
            return date.getDate() === today.getDate() &&
                date.getMonth() === today.getMonth() &&
                date.getFullYear() === today.getFullYear();
        },

        getEventsForDay(date) {
            const dateString = date.toISOString().split('T')[0];
            return this.events.filter(event => event.date === dateString);
        },

        prev() {
            if (this.calendarView === 'month') {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
            } else if (this.calendarView === 'week') {
                this.currentDate.setDate(this.currentDate.getDate() - 7);
                this.currentDate = new Date(this.currentDate); // trigger reactivity
            } else { // day view
                this.currentDate.setDate(this.currentDate.getDate() - 1);
                this.currentDate = new Date(this.currentDate);
            }
        },

        next() {
            if (this.calendarView === 'month') {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
            } else if (this.calendarView === 'week') {
                this.currentDate.setDate(this.currentDate.getDate() + 7);
                this.currentDate = new Date(this.currentDate);
            } else { // day view
                this.currentDate.setDate(this.currentDate.getDate() + 1);
                this.currentDate = new Date(this.currentDate);
            }
        },

        goToToday() {
            this.currentDate = new Date();
        },

        init() {
            const savedView = localStorage.getItem('kanban_view_mode');
            if (savedView) this.view = savedView;
            this.$watch('view', value => localStorage.setItem('kanban_view_mode', value));
        }
    };
};
