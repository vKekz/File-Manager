import { FileReplacementBehaviour } from "../../enums/file-replacement-behaviour.enum";

export interface UserStorageSettings {
  fileReplacementBehaviour: FileReplacementBehaviour;
  showFileHash: boolean;
  showUploadDate: boolean;
}
