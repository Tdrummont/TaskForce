import type { Holiday } from '../Components/useHolidayNotifications';

declare global {
    interface Window {
        $holidayToast?: {
            show: (holiday: Holiday, duration?: number) => void;
            clear?: () => void;
        };
    }
}

export {};
