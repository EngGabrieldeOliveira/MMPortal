import { get } from '../api/request';
import type { CockpitData } from '../../features/cockpit/types';

const ENDPOINT = 'dashboard';

export const CockpitService = {
  obterDashboard: (): Promise<CockpitData> => get<CockpitData>(ENDPOINT),
};
