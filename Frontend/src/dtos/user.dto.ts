import { UserSettings } from "./user/user-settings";

export interface UserDto {
  id: string;
  username: string;
  email: string;
  settings: UserSettings;
}
