import { UserStorageSettings } from "./user-storage-settings";
import { Theme } from "../../enums/theme.enum";

export interface UserSettings {
  storageSettings: UserStorageSettings;
  theme: Theme;
}
