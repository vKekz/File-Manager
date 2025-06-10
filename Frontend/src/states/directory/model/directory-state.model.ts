import { DirectoryDto } from "../../../dtos/directory.dto";
import { ApiResponse } from "../../../contracts/api.response";

export interface DirectoryStateModel {
  currentDirectory?: DirectoryDto;
  directories: DirectoryDto[];
  response?: ApiResponse;
}
