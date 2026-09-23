<?php

namespace App\Services;

use App\Models\Appointment;

class WhatsAppService
{
    /**
     * Generate the direct WhatsApp messaging link for appointment confirmation.
     */
    public static function generateConfirmationLink(Appointment $appointment): string
    {
        $phone = preg_replace('/[^0-9]/', '', $appointment->phone);
        
        // Add Mexico country code if 10 digits
        if (strlen($phone) === 10) {
            $phone = '52' . $phone;
        }

        $dateStr = $appointment->start_time->format('d/m/Y \a \l\a\s H:i');
        $therapistName = $appointment->user->name ?? 'Fisioterapeuta';
        
        $message = "Hola {$appointment->name}, te saludamos de Rehavité. 🩺\n\nTe recordamos tu cita agendada para el día *{$dateStr}* con el especialista *{$therapistName}*.\n\nPor favor responde con *SI* para confirmar o comunícate con nosotros para reprogramar. ¡Que tengas un excelente día!";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
