type Participant = {
    id: number;
    name: string | null;
    role: string;
};

type Props = {
    participants: Participant[];
};

export default function MeetingParticipantsList({ participants }: Props) {
    if (participants.length === 0) {
        return (
            <p className="text-sm text-plomo dark:text-slate-400">Sin participantes registrados.</p>
        );
    }

    return (
        <ul className="divide-y divide-plomo/10 dark:divide-white/10">
            {participants.map((p) => (
                <li key={p.id} className="flex items-center justify-between py-2 text-sm">
                    <span className="font-medium text-navy-900 dark:text-slate-100">{p.name ?? '—'}</span>
                    <span className="text-xs uppercase text-plomo dark:text-slate-400">{p.role}</span>
                </li>
            ))}
        </ul>
    );
}
